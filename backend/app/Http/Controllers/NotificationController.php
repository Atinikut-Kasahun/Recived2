<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->get();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count()
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $notification = $request->user()->notifications()->findOrFail($id);
        $data = $notification->data;

        $path = null;
        $name = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('replies', 'public');
            $name = $request->file('file')->getClientOriginalName();
        }

        // CASE 1: Reply to an Applicant (from their portal inquiry)
        $applicantId = $data['applicant_id'] ?? null;
        if (($data['type'] ?? '') === 'applicant_message' && $applicantId) {
            $applicant = \App\Models\Applicant::findOrFail($applicantId);
            $applicant->notify(new \App\Notifications\DirectMessage(
                $request->user()->name,
                $request->user()->id,
                $request->message,
                $applicant->name,
                $applicant->id,
                $path,
                $name
            ));
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        // CASE 2: Reply to another internal User
        $senderId = $data['sender_id'] ?? null;
        if (!$senderId) {
            return response()->json(['error' => 'Cannot reply - no sender found on this notification.'], 422);
        }

        $sender = \App\Models\User::findOrFail($senderId);
        $replierName = $request->user()->name;

        // Safely get candidate context
        $applicantName = $applicantId
            ? (optional(\App\Models\Applicant::find($applicantId))->name ?? 'a candidate')
            : ($data['candidate_name'] ?? 'a candidate');

        $sender->notify(new \App\Notifications\DirectReply(
            $replierName,
            $request->user()->id,
            $request->message,
            $applicantName
            // Note: DirectReply might need to be updated too if cross-team file sharing is needed
        ));

        // Also mark original as read
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
    public function togglePin(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->is_pinned = !$notification->is_pinned;
        $notification->save();

        return response()->json(['success' => true, 'is_pinned' => $notification->is_pinned]);
    }

    public function destroy(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function downloadAttachment(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $data = $notification->data;
        $path = $data['attachment_path'] ?? null;

        if (!$path || !file_exists(storage_path('app/public/' . $path))) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Use response()->file to allow inline viewing in the browser
        return response()->file(storage_path('app/public/' . $path), [
            'Content-Disposition' => 'inline; filename="' . ($data['attachment_name'] ?? 'attachment.pdf') . '"',
            'Access-Control-Allow-Origin' => '*',
            'X-Frame-Options' => 'ALLOWALL',
            'Content-Security-Policy' => "frame-ancestors *",
        ]);
    }

    /**
     * View attachment inline in the browser via a direct URL.
     * Accepts ?token= query param — MockApiAuth middleware resolves the user.
     */
    public function viewAttachment(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $data = $notification->data;
        $path = $data['attachment_path'] ?? null;

        if (!$path || !file_exists(storage_path('app/public/' . $path))) {
            abort(404, 'File not found');
        }

        $fullPath = storage_path('app/public/' . $path);
        $mimeType = 'application/pdf';
        try {
            $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);
        } catch (\Exception $e) {
        }

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . ($data['attachment_name'] ?? 'attachment.pdf') . '"',
        ]);
    }
}

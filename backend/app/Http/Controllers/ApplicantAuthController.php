<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApplicantAuthController extends Controller
{
    /**
     * Register an applicant account (called right after submitting an application).
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find the applicant by email (most recent, in case of multiple applications)
        $applicant = Applicant::where('email', $request->email)
            ->latest()
            ->first();

        if (!$applicant) {
            return response()->json(['message' => 'No application found with this email address.'], 404);
        }

        if ($applicant->password) {
            return response()->json(['message' => 'An account already exists for this email. Please log in.'], 409);
        }

        $token = Str::random(60);
        $applicant->update([
            'password' => Hash::make($request->password),
            'applicant_token' => $token,
        ]);

        return response()->json([
            'message' => 'Account created successfully.',
            'token' => $token,
            'applicant' => $this->formatApplicant($applicant),
        ], 201);
    }

    /**
     * Login with email + password.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $applicant = Applicant::where('email', $request->email)
            ->whereNotNull('password')
            ->latest()
            ->first();

        if (!$applicant || !Hash::check($request->password, $applicant->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        $token = Str::random(60);
        $applicant->update(['applicant_token' => $token]);

        return response()->json([
            'token' => $token,
            'applicant' => $this->formatApplicant($applicant),
        ]);
    }

    /**
     * Get the authenticated applicant's profile and all their applications.
     */
    public function me(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $applicant = Applicant::where('applicant_token', $token)->first();

        if (!$applicant) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        // Fetch ALL applications by same email
        $applications = Applicant::where('email', $applicant->email)
            ->with(['jobPosting', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'status' => $app->status,
                    'created_at' => $app->created_at,
                    'hired_at' => $app->hired_at,
                    'match_score' => $app->match_score,
                    'job_posting' => $app->jobPosting ? [
                        'id' => $app->jobPosting->id,
                        'title' => $app->jobPosting->title,
                        'department' => $app->jobPosting->department,
                        'location' => $app->jobPosting->location,
                        'type' => $app->jobPosting->type,
                        'created_at' => $app->jobPosting->created_at,
                    ] : null,
                    'company' => $app->tenant ? $app->tenant->name : 'Droga Pharma',
                    'hiring_team' => \App\Models\User::where('tenant_id', $app->tenant_id)
                        ->whereHas('roles', function ($q) {
                            $q->where('slug', 'ta_manager');
                        })
                        ->get(['name', 'email'])
                ];
            });

        return response()->json([
            'applicant' => $this->formatApplicant($applicant),
            'applications' => $applications,
        ]);
    }

    /**
     * Logout by clearing the token.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if ($token) {
            Applicant::where('applicant_token', $token)->update(['applicant_token' => null]);
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }

    private function formatApplicant(Applicant $applicant): array
    {
        return [
            'id' => $applicant->id,
            'name' => $applicant->name,
            'email' => $applicant->email,
            'phone' => $applicant->phone,
            'headline' => $applicant->headline,
            'age' => $applicant->age,
            'gender' => $applicant->gender,
            'years_of_experience' => $applicant->years_of_experience,
            'professional_background' => $applicant->professional_background,
            'portfolio_link' => $applicant->portfolio_link,
            'photo_path' => $applicant->photo_path,
            'resume_path' => $applicant->resume_path,
        ];
    }

    /**
     * Send a password reset code to the applicant's email.
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $applicant = Applicant::where('email', $request->email)->latest()->first();

        if (!$applicant) {
            // Return success anyway to prevent email enumeration (security best practice)
            return response()->json(['message' => 'If an account exists for this email, a reset code has been sent.']);
        }

        // Generate a 6-digit numeric code for easy entry
        $code = (string) random_int(100000, 999999);

        $applicant->update([
            'password_reset_token' => Hash::make($code),
            'password_reset_expires_at' => Carbon::now()->addMinutes(30),
        ]);

        // Send email (In dev with MAIL_MAILER=log, this prints to storage/logs/laravel.log)
        Mail::raw("Your password reset code is: {$code}\n\nThis code will expire in 30 minutes.", function ($msg) use ($applicant) {
            $msg->to($applicant->email)->subject('Password Reset Code - Droga Hiring Hub');
        });

        return response()->json(['message' => 'If an account exists for this email, a reset code has been sent.']);
    }

    /**
     * Reset the password using the code.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $applicant = Applicant::where('email', $request->email)->latest()->first();

        if (!$applicant || !$applicant->password_reset_token || !$applicant->password_reset_expires_at) {
            return response()->json(['message' => 'Invalid or expired reset code.'], 400);
        }

        if (Carbon::now()->isAfter($applicant->password_reset_expires_at)) {
            return response()->json(['message' => 'This reset code has expired. Please request a new one.'], 400);
        }

        if (!Hash::check($request->code, $applicant->password_reset_token)) {
            return response()->json(['message' => 'Invalid reset code.'], 400);
        }

        // Success - update password and clear reset data
        $token = Str::random(60);
        $applicant->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
            'applicant_token' => $token, // log them in automatically
        ]);

        return response()->json([
            'message' => 'Password reset successfully.',
            'token' => $token,
            'applicant' => $this->formatApplicant($applicant),
        ]);
    }

    /**
     * Update applicant profile details (name, phone, headline)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant) {
            return response()->json(['message' => 'Invalid session.'], 401);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'headline' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:50',
            'years_of_experience' => 'nullable|integer',
            'professional_background' => 'nullable|string',
            'portfolio_link' => 'nullable|url|max:255',
            'resume' => 'nullable|file|mimes:pdf|max:10000',
        ]);

        $applicant->update([
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'headline' => $validated['headline'],
            'phone' => $validated['phone'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'years_of_experience' => $validated['years_of_experience'],
            'professional_background' => $validated['professional_background'],
            'portfolio_link' => $validated['portfolio_link'],
        ]);

        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $applicant->update(['resume_path' => $path]);
        }

        return response()->json([
            'message' => 'Profile updated successfully.',
            'applicant' => $this->formatApplicant($applicant),
        ]);
    }
    /**
     * Get notifications for the applicant.
     */
    public function notifications(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant)
            return response()->json(['message' => 'Invalid session.'], 401);

        return response()->json([
            'notifications' => $applicant->notifications()->latest()->get(),
            'unread_count' => $applicant->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markNotificationRead(Request $request, $id): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant)
            return response()->json(['message' => 'Invalid session.'], 401);

        $notification = $applicant->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(Request $request, $id): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant)
            return response()->json(['message' => 'Invalid session.'], 401);

        $notification = $applicant->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant)
            return response()->json(['message' => 'Invalid session.'], 401);

        $applicant->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Send a message to the TA team for a specific application.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant)
            return response()->json(['message' => 'Invalid session.'], 401);

        $request->validate([
            'application_id' => 'required|exists:applicants,id',
            'message' => 'required|string|max:2000',
        ]);

        $application = Applicant::with('jobPosting')->findOrFail($request->application_id);

        if ($application->email !== $applicant->email) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $path = null;
        $name = null;

        $recipients = \App\Models\User::where('tenant_id', $application->tenant_id)
            ->whereHas('roles', function ($q) {
                $q->where('slug', 'ta_manager');
            })->get();

        if ($recipients->isEmpty()) {
            return response()->json(['error' => 'No TA team identified for this company. Please try again later.'], 404);
        }

        /** @var \App\Models\User $user */
        foreach ($recipients as $user) {
            $user->notify(new \App\Notifications\ApplicantMessage(
                $applicant,
                $request->message,
                $application->jobPosting->title ?? 'Position',
                $path,
                $name
            ));
        }

        // --- ADDED PER USER REQUEST: Save sent message status for the applicant to see too ---
        $applicant->notify(new \App\Notifications\SentMessage(
            $request->message,
            $application->company ?? 'the TA team',
            $path,
            $name
        ));

        return response()->json(['success' => true]);
    }

    /**
     * Download an attachment from a notification.
     */
    public function downloadNotificationAttachment(Request $request, $id)
    {
        $token = $request->bearerToken();
        if (!$token)
            return response()->json(['message' => 'Unauthenticated.'], 401);

        $applicant = Applicant::where('applicant_token', $token)->first();
        if (!$applicant)
            return response()->json(['message' => 'Invalid session.'], 401);

        $notification = $applicant->notifications()->findOrFail($id);
        $path = $notification->data['attachment_path'] ?? null;

        if (!$path || !file_exists(storage_path('app/public/' . $path))) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->file(storage_path('app/public/' . $path), [
            'Content-Disposition' => 'inline; filename="' . ($notification->data['attachment_name'] ?? 'attachment.pdf') . '"'
        ]);
    }
}

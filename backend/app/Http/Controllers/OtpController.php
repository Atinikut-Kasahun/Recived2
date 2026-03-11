<?php

namespace App\Http\Controllers;

use App\Mail\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /**
     * POST /v1/public/send-otp
     * Generate a 6-digit OTP, cache it for 10 minutes, and email it.
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'A valid email address is required.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // Rate limit: max 3 OTP requests per email per 10 minutes
        $rateLimitKey = 'otp_rate_limit:' . $email;
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts >= 3) {
            return response()->json([
                'message' => 'Too many requests. Please wait a few minutes before requesting a new code.',
            ], 429);
        }

        // Generate 6-digit code
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'otp:' . $email;

        // Store OTP in cache for 10 minutes
        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        // Increment rate limit counter (resets after 10 minutes)
        Cache::put($rateLimitKey, $attempts + 1, now()->addMinutes(10));

        // Send the email
        try {
            Mail::to($email)->send(new OtpVerification($otp, $email));
        } catch (\Exception $e) {
            \Log::error('OTP mail failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send verification email. Please check the address and try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Verification code sent. Please check your email.',
        ], 200);
    }

    /**
     * POST /v1/public/verify-otp
     * Compare submitted OTP against cached value.
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Email and a 6-digit code are required.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->email));
        $cacheKey = 'otp:' . $email;
        $stored = Cache::get($cacheKey);

        if (!$stored) {
            return response()->json([
                'message' => 'Code expired or not found. Please request a new one.',
            ], 422);
        }

        if ($stored !== $request->otp) {
            return response()->json([
                'message' => 'Incorrect code. Please try again.',
            ], 422);
        }

        // ✅ Valid — delete so it can't be reused
        Cache::forget($cacheKey);
        Cache::forget('otp_rate_limit:' . $email);

        return response()->json([
            'message' => 'Email verified successfully.',
            'email' => $email,
            'verified' => true,
        ], 200);
    }
}

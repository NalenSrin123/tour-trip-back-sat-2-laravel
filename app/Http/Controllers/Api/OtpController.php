<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordWithOtpRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    /**
     * Send OTP to the user's email for password reset.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->validated('email')));

        // Rate limiting check (cooldown: 60 seconds)
        $recentOtp = PasswordResetOtp::where('email', $email)
            ->where('created_at', '>=', now()->subSeconds(60))
            ->first();

        if ($recentOtp) {
            $remainingSeconds = 60 - (int) now()->diffInSeconds($recentOtp->created_at);
            return response()->json([
                'success'             => false,
                'message'             => "Please wait {$remainingSeconds} seconds before requesting a new OTP.",
                'retry_after_seconds' => max(1, $remainingSeconds),
            ], 429);
        }

        // Generate a 6-digit OTP code
        $otpCode = (string) random_int(100000, 999999);
        $expiresInMinutes = 10;
        $expiresAt = now()->addMinutes($expiresInMinutes);

        // Invalidate / clean up previous unverified OTP records for this email
        PasswordResetOtp::where('email', $email)
            ->where('is_verified', false)
            ->delete();

        // Create new OTP record
        PasswordResetOtp::create([
            'email'       => $email,
            'otp_code'    => $otpCode,
            'expires_at'  => $expiresAt,
            'is_verified' => false,
        ]);

        // Send Email
        try {
            Mail::to($email)->send(new PasswordResetOtpMail($otpCode, $expiresInMinutes));
        } catch (\Throwable $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage(), [
                'email'     => $email,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP email. Please try again later.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        $response = [
            'success' => true,
            'message' => 'OTP code has been sent to your email successfully.',
            'data'    => [
                'email'              => $email,
                'expires_in_minutes' => $expiresInMinutes,
            ],
        ];

        // Include debug_otp in local or debug environments for developer convenience
        if (config('app.debug') || app()->environment('local', 'testing')) {
            $response['debug_otp'] = $otpCode;
        }

        return response()->json($response, 200);
    }

    /**
     * Verify OTP code provided by the user.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->validated('email')));
        $otpCode = trim($request->validated('otp_code'));

        // Retrieve latest unverified OTP record
        $otpRecord = PasswordResetOtp::where('email', $email)
            ->where('is_verified', false)
            ->latest('created_at')
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No active OTP request found for this email address.',
            ], 404);
        }

        // Check if expired
        if ($otpRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP code has expired. Please request a new one.',
            ], 400);
        }

        // Check code match
        if (!hash_equals((string) $otpRecord->otp_code, (string) $otpCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code. Please check and try again.',
            ], 400);
        }

        // Generate secure 64-char reset token for subsequent password reset
        $resetToken = Str::random(64);

        // Mark OTP as verified and store token
        $otpRecord->update([
            'is_verified' => true,
            'token'       => $resetToken,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'data'    => [
                'email'       => $email,
                'reset_token' => $resetToken,
            ],
        ], 200);
    }

    /**
     * Reset the user's password using the verified reset_token.
     */
    public function resetPassword(ResetPasswordWithOtpRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->validated('email')));
        $resetToken = $request->validated('reset_token');
        $newPassword = $request->validated('password');

        // Look up verified OTP record by email & reset_token
        $otpRecord = PasswordResetOtp::where('email', $email)
            ->where('token', $resetToken)
            ->where('is_verified', true)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired password reset token.',
            ], 400);
        }

        // Disallow token reuse older than 30 minutes
        if ($otpRecord->updated_at->diffInMinutes(now()) > 30) {
            $otpRecord->delete();
            return response()->json([
                'success' => false,
                'message' => 'Password reset token has expired. Please verify OTP again.',
            ], 400);
        }

        // Find user and update password
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        // Delete the consumed OTP record
        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully. You can now log in with your new password.',
        ], 200);
    }
}

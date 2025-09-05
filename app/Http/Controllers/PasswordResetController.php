<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link to user email
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->validated()['email'];

        // Generate secure random token
        $token = Str::random(64);

        // Delete any existing reset tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Create new reset token record
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Create reset URL (you can customize this based on your frontend)
        $resetUrl = (config('app.frontend_url') ?: config('app.url')) . '/reset-password?' . http_build_query([
            'token' => $token,
            'email' => $email
        ]);

        // Send email
        try {
            Mail::to($email)->send(new PasswordResetMail($resetUrl, $email));

            return response()->json([
                'message' => 'Password reset link sent to your email address.'
            ], 200);
        } catch (\Exception $e) {
            // Clean up token if email fails
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return response()->json([
                'message' => 'Failed to send password reset email. Please try again.'
            ], 500);
        }
    }

    /**
     * Reset user password using token
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Find the reset token record
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'message' => 'Invalid or expired reset token.'
            ], 400);
        }

        // Check if token is valid (not expired - 60 minutes)
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            // Delete expired token
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return response()->json([
                'message' => 'Reset token has expired. Please request a new one.'
            ], 400);
        }

        // Verify token
        if (!Hash::check($validated['token'], $resetRecord->token)) {
            return response()->json([
                'message' => 'Invalid reset token.'
            ], 400);
        }

        // Find and update user password
        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 404);
        }

        // Update password
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Revoke all existing tokens for security
        $user->tokens()->delete();

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return response()->json([
            'message' => 'Password has been reset successfully. Please login with your new password.'
        ], 200);
    }

    /**
     * Verify if reset token is valid
     */
    public function verifyToken(string $token, string $email): JsonResponse
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid reset token.'
            ], 400);
        }

        // Check if token is expired
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return response()->json([
                'valid' => false,
                'message' => 'Reset token has expired.'
            ], 400);
        }

        // Verify token
        if (!Hash::check($token, $resetRecord->token)) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid reset token.'
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Reset token is valid.',
            'expires_in_minutes' => 60 - $tokenAge
        ], 200);
    }
}

<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * Generate and store an OTP for a user.
     */
    public function generateOtp(User $user, string $channel, int $length = 6, int $expiryMinutes = 10): string
    {
        // Generate random OTP
        $otp = str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);

        // Create a unique identifier for this OTP attempt
        $otpId = Str::uuid();

        // Store OTP temporarily in cache with short expiry (for verification)
        Cache::put("otp_verification_{$otpId}", $otp, now()->addMinutes($expiryMinutes));

        // Create hash with additional entropy for database storage
        $otpHash = Hash::make($otp . Str::random(16));

        // Store the verification record
        OtpVerification::create([
            'user_id' => $user->id,
            'channel' => $channel,
            'otp_hash' => $otpHash,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        // Store the OTP ID in cache for verification lookup
        Cache::put("otp_id_{$user->id}_{$channel}", $otpId, now()->addMinutes($expiryMinutes));

        return $otp;
    }

    /**
     * Verify an OTP for a user.
     */
    public function verifyOtp(User $user, string $channel, string $otp): bool
    {
        // Get the OTP ID from cache
        $otpId = Cache::get("otp_id_{$user->id}_{$channel}");

        if (!$otpId) {
            return false; // No active OTP for this user/channel
        }

        // Get the stored OTP from cache
        $storedOtp = Cache::get("otp_verification_{$otpId}");

        if (!$storedOtp) {
            return false; // OTP expired or doesn't exist
        }

        // Verify the OTP
        if (!hash_equals($storedOtp, $otp)) {
            return false; // OTP doesn't match
        }

        // Mark as verified in database
        $verification = OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->unverified()
            ->notExpired()
            ->latest()
            ->first();

        if ($verification) {
            $verification->markAsVerified();
        }

        // Clean up cache entries
        Cache::forget("otp_verification_{$otpId}");
        Cache::forget("otp_id_{$user->id}_{$channel}");

        return true;
    }

    /**
     * Check if user has a valid (unverified, non-expired) OTP.
     */
    public function hasValidOtp(User $user, string $channel): bool
    {
        return OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->valid()
            ->exists();
    }

    /**
     * Get the latest OTP verification for a user and channel.
     */
    public function getLatestVerification(User $user, string $channel): ?OtpVerification
    {
        return OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->latest()
            ->first();
    }

    /**
     * Clean up expired OTPs (can be called by a scheduled job).
     */
    public function cleanupExpiredOtps(): int
    {
        return OtpVerification::where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Revoke all active OTPs for a user and channel.
     */
    public function revokeActiveOtps(User $user, string $channel): void
    {
        // Clean up cache entries
        $otpId = Cache::get("otp_id_{$user->id}_{$channel}");
        if ($otpId) {
            Cache::forget("otp_verification_{$otpId}");
            Cache::forget("otp_id_{$user->id}_{$channel}");
        }

        // Mark all unverified OTPs as verified (effectively revoking them)
        OtpVerification::where('user_id', $user->id)
            ->where('channel', $channel)
            ->unverified()
            ->update(['verified_at' => now()]);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and store a random OTP
     * 
     * @param string $key Unique identifier (e.g., email or user ID)
     * @param int $length
     * @param int $expiryMinutes
     * @return string
     */
    public function generateOtp(string $key, int $length = 6, int $expiryMinutes = 10): string
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }

        // Store in cache
        $cacheKey = "otp_{$key}";
        Cache::put($cacheKey, $otp, now()->addMinutes($expiryMinutes));

        Log::info('OTP generated', ['key' => $key, 'expiry' => $expiryMinutes]);

        return $otp;
    }

    /**
     * Verify the provided OTP
     * 
     * @param string $key
     * @param string $otp
     * @return bool
     */
    public function verifyOtp(string $key, string $otp): bool
    {
        $cacheKey = "otp_{$key}";
        $storedOtp = Cache::get($cacheKey);

        if ($storedOtp && $storedOtp === $otp) {
            Cache::forget($cacheKey);
            return true;
        }

        Log::warning('OTP verification failed', ['key' => $key]);
        return false;
    }
}

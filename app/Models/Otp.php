<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Otp extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'type',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a new OTP for the given phone number
     */
    public static function generateOtp(string $phone, string $type = 'registration', int $length = 6): self
    {
        // Delete any existing unused OTPs for this phone and type
        self::where('phone', $phone)
            ->where('type', $type)
            ->where('is_used', false)
            ->delete();

        // Generate OTP with specified length
        $max = pow(10, $length) - 1;
        $otp = str_pad(rand(0, $max), $length, '0', STR_PAD_LEFT);

        // Get OTP settings for expiry
        $otpSettings = \App\Models\OtpSetting::getSettings();
        $expiryMinutes = $otpSettings->otp_expiry_minutes;

        // Create new OTP record
        return self::create([
            'phone' => $phone,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }

    /**
     * Verify OTP
     */
    public static function verifyOtp(string $phone, string $otp, string $type = 'registration'): bool
    {
        try {
            \Log::info('Otp::verifyOtp called', [
                'phone' => $phone,
                'otp' => $otp,
                'type' => $type
            ]);

            $otpRecord = self::where('phone', $phone)
                ->where('otp', $otp)
                ->where('type', $type)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            \Log::info('OTP record lookup result', [
                'phone' => $phone,
                'type' => $type,
                'otp_found' => $otpRecord ? true : false,
                'otp_id' => $otpRecord ? $otpRecord->id : null,
                'otp_used' => $otpRecord ? $otpRecord->is_used : null,
                'otp_expires' => $otpRecord ? $otpRecord->expires_at : null
            ]);

            if ($otpRecord) {
                $otpRecord->update(['is_used' => true]);
                \Log::info('OTP marked as used', [
                    'otp_id' => $otpRecord->id,
                    'phone' => $phone
                ]);
                return true;
            }

            \Log::info('OTP verification failed - no valid record found', [
                'phone' => $phone,
                'type' => $type
            ]);
            return false;
        } catch (\Exception $e) {
            \Log::error('Error in Otp::verifyOtp', [
                'phone' => $phone,
                'otp' => $otp,
                'type' => $type,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Check if phone has a valid unused OTP
     */
    public static function hasValidOtp(string $phone, string $type = 'registration'): bool
    {
        return self::where('phone', $phone)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->exists();
    }
}

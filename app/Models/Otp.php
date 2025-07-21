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
    public static function generateOtp(string $phone, string $type = 'registration'): self
    {
        // Delete any existing unused OTPs for this phone and type
        self::where('phone', $phone)
            ->where('type', $type)
            ->where('is_used', false)
            ->delete();

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create new OTP record
        return self::create([
            'phone' => $phone,
            'otp' => $otp,
            'type' => $type,
            'expires_at' => now()->addMinutes(10), // OTP expires in 10 minutes
        ]);
    }

    /**
     * Verify OTP
     */
    public static function verifyOtp(string $phone, string $otp, string $type = 'registration'): bool
    {
        $otpRecord = self::where('phone', $phone)
            ->where('otp', $otp)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            $otpRecord->update(['is_used' => true]);
            return true;
        }

        return false;
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'otp_length',
        'otp_expiry_minutes',
        'max_attempts',
        'resend_cooldown_seconds',
        'require_otp_for_registration',
        'require_otp_for_tenant_registration',
        'require_otp_for_login',
        'require_otp_for_password_reset',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'require_otp_for_registration' => 'boolean',
        'require_otp_for_tenant_registration' => 'boolean',
        'require_otp_for_login' => 'boolean',
        'require_otp_for_password_reset' => 'boolean',
    ];

    /**
     * Get OTP settings (singleton)
     */
    public static function getSettings()
    {
        $settings = static::first();
        
        if (!$settings) {
            // Create default settings if none exist
            $settings = static::create([
                'is_enabled' => true,
                'otp_length' => 6,
                'otp_expiry_minutes' => 10,
                'max_attempts' => 5,
                'resend_cooldown_seconds' => 30,
                'require_otp_for_registration' => true,
                'require_otp_for_tenant_registration' => false,
                'require_otp_for_login' => false,
                'require_otp_for_password_reset' => true,
            ]);
        }
        
        return $settings;
    }

    /**
     * Check if OTP is enabled for specific action
     */
    public function isOtpRequiredFor($action)
    {
        if (!$this->is_enabled) {
            return false;
        }

        switch ($action) {
            case 'registration':
                return $this->require_otp_for_registration;
            case 'tenant_registration':
                return $this->require_otp_for_tenant_registration;
            case 'login':
                return $this->require_otp_for_login;
            case 'password_reset':
                return $this->require_otp_for_password_reset;
            default:
                return false;
        }
    }
}

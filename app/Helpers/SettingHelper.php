<?php

namespace App\Helpers;

use App\Models\PaymentMethod;
use App\Models\Setting;

class SettingHelper
{
    /**
     * Get bKash sandbox mode status
     */
    public static function getBkashSandboxStatus()
    {
        $bkashMethod = PaymentMethod::where('code', 'bkash')->first();

        if (!$bkashMethod) {
            return [
                'enabled' => false,
                'status' => 'Not Configured',
                'message' => 'bKash payment method is not configured'
            ];
        }

        $settings = $bkashMethod->settings ?? [];
        $sandboxMode = $settings['sandbox_mode'] ?? false;

        return [
            'enabled' => $sandboxMode,
            'status' => $sandboxMode ? 'Sandbox Mode' : 'Live Mode',
            'message' => $sandboxMode
                ? 'bKash is running in sandbox mode for testing'
                : 'bKash is running in live mode for production',
            'is_active' => $bkashMethod->is_active,
            'configured' => !empty($settings['merchant_id']) && !empty($settings['api_key']) && !empty($settings['api_secret'])
        ];
    }

    /**
     * Get all payment methods status
     */
    public static function getPaymentMethodsStatus()
    {
        $methods = PaymentMethod::all();
        $status = [];

        foreach ($methods as $method) {
            $settings = $method->settings ?? [];
            $status[$method->code] = [
                'name' => $method->name,
                'is_active' => $method->is_active,
                'transaction_fee' => $method->transaction_fee,
                'sandbox_mode' => $settings['sandbox_mode'] ?? false,
                'configured' => !empty($settings['merchant_id']) && !empty($settings['api_key']) && !empty($settings['api_secret'])
            ];
        }

        return $status;
    }

    /**
     * Get setting value with default fallback
     */
    public static function setting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     */
    public static function setSetting($key, $value)
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}

<?php

namespace App\Helpers;

use App\Models\PaymentMethod;

class BkashHelper
{
        /**
     * Validate bKash credentials
     */
    public static function validateCredentials($merchantId, $merchantPassword, $apiKey, $apiSecret)
    {
        $errors = [];

        // Check if all fields are filled
        if (empty($merchantId)) {
            $errors[] = 'Merchant ID is required';
        }

        if (empty($merchantPassword)) {
            $errors[] = 'Merchant Password is required';
        }

        if (empty($apiKey)) {
            $errors[] = 'API Key is required';
        }

        if (empty($apiSecret)) {
            $errors[] = 'API Secret is required';
        }

        // Check format
        if (!empty($merchantId) && strlen($merchantId) < 5) {
            $errors[] = 'Merchant ID seems too short';
        }

        if (!empty($merchantPassword) && strlen($merchantPassword) < 5) {
            $errors[] = 'Merchant Password seems too short';
        }

        if (!empty($apiKey) && strlen($apiKey) < 10) {
            $errors[] = 'API Key seems too short';
        }

        if (!empty($apiSecret) && strlen($apiSecret) < 10) {
            $errors[] = 'API Secret seems too short';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get bKash credentials from database
     */
    public static function getCredentials()
    {
        $bkashMethod = PaymentMethod::where('code', 'bkash')->first();

        if (!$bkashMethod) {
            return null;
        }

        $settings = $bkashMethod->settings ?? [];

        return [
            'merchant_id' => $settings['merchant_id'] ?? '',
            'api_key' => $settings['api_key'] ?? '',
            'api_secret' => $settings['api_secret'] ?? '',
            'gateway_url' => $settings['gateway_url'] ?? '',
            'sandbox_mode' => $settings['sandbox_mode'] ?? false,
            'is_active' => $bkashMethod->is_active
        ];
    }

    /**
     * Check if bKash is properly configured
     */
    public static function isConfigured()
    {
        $credentials = self::getCredentials();

        if (!$credentials) {
            return false;
        }

        return !empty($credentials['merchant_id']) &&
               !empty($credentials['api_key']) &&
               !empty($credentials['api_secret']);
    }
}

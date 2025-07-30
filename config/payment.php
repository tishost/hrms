<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for different payment gateways
    | used in the application.
    |
    */

    'default' => env('PAYMENT_DEFAULT', 'bkash'),

    'bkash' => [
        'merchant_id' => env('BKASH_MERCHANT_ID', 'BKASH001'),
        'api_key' => env('BKASH_API_KEY', 'demo_key'),
        'api_secret' => env('BKASH_API_SECRET', 'demo_secret'),
        'gateway_url' => env('BKASH_GATEWAY_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
        'sandbox_mode' => env('BKASH_SANDBOX', true),
        'transaction_fee' => 1.5,
        'tokenized_checkout' => true,
    ],

    'nagad' => [
        'merchant_id' => env('NAGAD_MERCHANT_ID', 'NAGAD001'),
        'api_key' => env('NAGAD_API_KEY', 'demo_key'),
        'api_secret' => env('NAGAD_API_SECRET', 'demo_secret'),
        'gateway_url' => env('NAGAD_GATEWAY_URL', 'https://nagad.com.bd/payment'),
        'is_sandbox' => env('NAGAD_SANDBOX', true),
        'transaction_fee' => 1.0,
    ],

    'rocket' => [
        'merchant_id' => env('ROCKET_MERCHANT_ID', 'ROCKET001'),
        'api_key' => env('ROCKET_API_KEY', 'demo_key'),
        'api_secret' => env('ROCKET_API_SECRET', 'demo_secret'),
        'gateway_url' => env('ROCKET_GATEWAY_URL', 'https://rocket.com.bd/payment'),
        'is_sandbox' => env('ROCKET_SANDBOX', true),
        'transaction_fee' => 1.8,
    ],

    'bank_transfer' => [
        'bank_name' => env('BANK_NAME', 'Demo Bank'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'HRMS System'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
        'branch' => env('BANK_BRANCH', 'Dhaka Main Branch'),
        'routing_number' => env('BANK_ROUTING_NUMBER', '123456789'),
        'transaction_fee' => 0.0,
    ],

    'cash' => [
        'office_address' => env('CASH_OFFICE_ADDRESS', 'Dhaka, Bangladesh'),
        'office_hours' => env('CASH_OFFICE_HOURS', '9:00 AM - 5:00 PM'),
        'contact_number' => env('CASH_CONTACT_NUMBER', '+880 1234567890'),
        'transaction_fee' => 0.0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    */

    'currency' => 'BDT',
    'currency_symbol' => '৳',

    'invoice_prefix' => 'INV',
    'invoice_number_format' => 'INV-{YEAR}-{SEQUENCE}',

    'payment_timeout' => 30, // minutes
    'auto_cancel_pending' => true,

    'success_url' => env('PAYMENT_SUCCESS_URL', '/owner/subscription/payment/success'),
    'cancel_url' => env('PAYMENT_CANCEL_URL', '/owner/subscription/payment/cancel'),
    'fail_url' => env('PAYMENT_FAIL_URL', '/owner/subscription/payment/fail'),
];

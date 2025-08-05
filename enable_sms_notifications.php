<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

try {
    echo "=== Enabling SMS Notifications ===\n\n";

    // Enable SMS notifications
    $settings = [
        'sms_enabled' => '1',
        'owner_welcome_sms' => '1',
        'system_otp_sms' => '1',
        'system_welcome_sms' => '1'
    ];

    foreach ($settings as $key => $value) {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Enabled: $key\n";
    }

    // Check current settings
    echo "\n=== Current SMS Settings ===\n";
    $smsSettings = Setting::where('key', 'like', '%sms%')->get();
    foreach ($smsSettings as $setting) {
        echo "- {$setting->key}: {$setting->value}\n";
    }

    echo "\n✅ SMS notifications enabled successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;

try {
    echo "=== Adding Missing SMS Settings ===\n\n";

    // Add missing SMS settings
    $smsSettings = [
        'sms_provider' => 'bulksms',
        'sms_api_key' => 'lJp4X9JWUFZ0tB2aivJc0OeC9zSQUhIZTnHRBGyl', // Use existing token as API key
        'sms_api_secret' => '', // Leave empty for now
        'sms_monthly_limit' => '1000',
        'sms_monthly_count' => '0'
    ];

    foreach ($smsSettings as $key => $value) {
        SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Added: $key = $value\n";
    }

    // Show all SMS settings
    echo "\n=== All SMS Settings ===\n";
    $allSmsSettings = SystemSetting::where('key', 'like', 'sms_%')->orderBy('key')->get();
    foreach ($allSmsSettings as $setting) {
        echo "- {$setting->key}: {$setting->value}\n";
    }

    echo "\n✅ Missing SMS settings added successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
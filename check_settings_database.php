<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

try {
    echo "=== Checking Settings Database ===\n\n";

    // Check all settings
    echo "1. All Settings:\n";
    $allSettings = Setting::all();
    echo "Total settings: " . $allSettings->count() . "\n\n";
    
    foreach ($allSettings as $setting) {
        echo "- Key: {$setting->key}\n";
        echo "  Value: " . (is_string($setting->value) ? $setting->value : json_encode($setting->value)) . "\n";
        echo "  Type: {$setting->type}\n";
        echo "  Description: {$setting->description}\n\n";
    }

    // Check SMS templates specifically
    echo "2. SMS Templates:\n";
    $smsTemplates = Setting::where('key', 'like', '%template%')->where('key', 'like', '%sms%')->get();
    foreach ($smsTemplates as $template) {
        echo "- {$template->key}: " . (is_string($template->value) ? $template->value : json_encode($template->value)) . "\n";
    }

    // Check SMS notification settings
    echo "\n3. SMS Notification Settings:\n";
    $smsSettings = Setting::where('key', 'like', '%sms%')->get();
    foreach ($smsSettings as $setting) {
        echo "- {$setting->key}: " . (is_string($setting->value) ? $setting->value : json_encode($setting->value)) . "\n";
    }

    // Check email templates
    echo "\n4. Email Templates:\n";
    $emailTemplates = Setting::where('key', 'like', '%template%')->where('key', 'like', '%email%')->get();
    foreach ($emailTemplates as $template) {
        echo "- {$template->key}: " . (is_string($template->value) ? $template->value : json_encode($template->value)) . "\n";
    }

    echo "\n=== Database Check Completed ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
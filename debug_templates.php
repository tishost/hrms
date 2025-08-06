<?php

require_once 'vendor/autoload.php';

use App\Models\SystemSetting;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging templates in database...\n\n";

// Get all template settings
$templates = SystemSetting::where('key', 'like', 'template_%')->get();

if ($templates->isEmpty()) {
    echo "❌ No templates found in database\n";
} else {
    echo "📋 Found " . $templates->count() . " templates:\n\n";
    
    foreach ($templates as $template) {
        echo "🔑 Key: " . $template->key . "\n";
        echo "📄 Value: " . $template->value . "\n";
        
        $data = json_decode($template->value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ JSON Valid\n";
            if (is_array($data)) {
                foreach ($data as $field => $value) {
                    echo "   {$field}: " . (empty($value) ? 'EMPTY' : $value) . "\n";
                }
            }
        } else {
            echo "❌ JSON Error: " . json_last_error_msg() . "\n";
        }
        echo "\n" . str_repeat("-", 50) . "\n\n";
    }
}

// Test specific template
$testTemplate = 'template_password_reset_otp_sms';
$testSetting = SystemSetting::where('key', $testTemplate)->first();

if ($testSetting) {
    echo "🧪 Testing {$testTemplate}:\n";
    echo "Raw value: " . $testSetting->value . "\n";
    
    $data = json_decode($testSetting->value, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        echo "Content: " . ($data['content'] ?? 'NULL') . "\n";
    } else {
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
} else {
    echo "❌ {$testTemplate} not found in database\n";
}

echo "\n🎉 Debug completed!\n";

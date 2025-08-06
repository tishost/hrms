<?php

require_once 'vendor/autoload.php';

use App\Models\SystemSetting;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing template save and load...\n\n";

// Test template data
$templateName = 'password_reset_otp_sms';
$testContent = 'Your HRMS password reset OTP is: {otp}. Valid for 10 minutes. Please enter this code to reset your password. If you didn\'t request this, please ignore. - HRMS';

echo "📝 Saving template: {$templateName}\n";
echo "📄 Content: {$testContent}\n\n";

// Save template
$templateData = [
    'content' => $testContent
];

$result = SystemSetting::setValue('template_' . $templateName, json_encode($templateData));

if ($result) {
    echo "✅ Template saved successfully!\n\n";
} else {
    echo "❌ Failed to save template!\n\n";
}

// Load template
echo "📖 Loading template: {$templateName}\n";
$template = SystemSetting::where('key', 'template_' . $templateName)->first();

if ($template) {
    echo "📄 Raw value: " . $template->value . "\n";
    
    $data = json_decode($template->value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ JSON Valid\n";
        echo "📝 Content: " . ($data['content'] ?? 'NULL') . "\n";
    } else {
        echo "❌ JSON Error: " . json_last_error_msg() . "\n";
    }
} else {
    echo "❌ Template not found in database!\n";
}

echo "\n🎉 Test completed!\n";

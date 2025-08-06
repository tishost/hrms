<?php

require_once 'vendor/autoload.php';

use App\Models\SystemSetting;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing null templates in database...\n";

// Templates to fix
$templates = [
    'template_password_reset_email' => [
        'subject' => 'Password Reset Request - HRMS',
        'content' => 'Dear {name},\n\nYou have requested to reset your password.\n\nYour OTP: {otp}\nValid for 10 minutes.\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nHRMS Team'
    ],
    'template_password_reset_otp_sms' => [
        'content' => 'Your HRMS password reset OTP is: {otp}. Valid for 10 minutes. Please enter this code to reset your password. If you didn\'t request this, please ignore. - HRMS'
    ],
    'template_otp_verification_sms' => [
        'content' => 'Your HRMS password reset OTP is: {otp}. Valid for 10 minutes. If you didn\'t request this, please ignore. - HRMS'
    ]
];

foreach ($templates as $key => $data) {
    $existing = SystemSetting::where('key', $key)->first();
    
    if ($existing) {
        $currentValue = json_decode($existing->value, true);
        
        // Check if current value is null or empty
        if (empty($currentValue) || $currentValue === null || 
            (isset($currentValue['content']) && empty($currentValue['content'])) ||
            (isset($currentValue['subject']) && empty($currentValue['subject']))) {
            
            SystemSetting::setValue($key, json_encode($data));
            echo "✅ Fixed: {$key}\n";
        } else {
            echo "⏭️  Skipped: {$key} (already has content)\n";
        }
    } else {
        // Create new template
        SystemSetting::setValue($key, json_encode($data));
        echo "✅ Created: {$key}\n";
    }
}

echo "\n🎉 Template fixing completed!\n";

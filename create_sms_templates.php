<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

// Create welcome SMS template
Setting::updateOrCreate(
    ['key' => 'template_welcome_sms'],
    ['value' => json_encode([
        'subject' => 'Welcome to HRMS',
        'content' => 'Welcome {{name}}! Your HRMS account is ready. Login at our website to get started.'
    ])]
);

// Create owner welcome SMS template
Setting::updateOrCreate(
    ['key' => 'template_owner_welcome_sms'],
    ['value' => json_encode([
        'subject' => 'Welcome to HRMS',
        'content' => 'Welcome {{name}}! Your HRMS owner account is ready. Start managing your properties today.'
    ])]
);

// Create payment confirmation SMS template
Setting::updateOrCreate(
    ['key' => 'template_payment_confirmation_sms'],
    ['value' => json_encode([
        'subject' => 'Payment Confirmation',
        'content' => 'Payment of ${{amount}} received. Invoice: {{invoice_number}}. Thank you!'
    ])]
);

// Create invoice reminder SMS template
Setting::updateOrCreate(
    ['key' => 'template_invoice_reminder_sms'],
    ['value' => json_encode([
        'subject' => 'Invoice Reminder',
        'content' => 'Invoice {{invoice_number}} for ${{amount}} is due on {{due_date}}. Please pay on time.'
    ])]
);

// Create OTP verification SMS template
Setting::updateOrCreate(
    ['key' => 'template_otp_verification_sms'],
    ['value' => json_encode([
        'subject' => 'OTP Verification',
        'content' => 'Your OTP is {{otp}}. Use this code to verify your account. Valid for 10 minutes.'
    ])]
);

echo "SMS templates created successfully!\n"; 
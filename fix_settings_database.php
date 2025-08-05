<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

try {
    echo "=== Fixing Settings Database ===\n\n";

    // 1. Check current settings
    echo "1. Current Settings Count: " . Setting::count() . "\n\n";

    // 2. Add missing SMS notification settings
    echo "2. Adding SMS Notification Settings:\n";
    $smsSettings = [
        'sms_enabled' => '1',
        'owner_welcome_sms' => '1',
        'system_otp_sms' => '1',
        'system_welcome_sms' => '1',
        'system_password_reset_sms' => '1',
        'system_security_alert_sms' => '1',
        'owner_package_purchase_sms' => '1',
        'owner_payment_confirmation_sms' => '1',
        'owner_invoice_reminder_sms' => '1',
        'owner_subscription_expiry_sms' => '1',
        'owner_subscription_renewal_sms' => '1',
        'tenant_welcome_sms' => '1',
        'tenant_rent_reminder_sms' => '1',
        'tenant_payment_confirmation_sms' => '1',
        'tenant_maintenance_update_sms' => '1',
        'tenant_checkout_reminder_sms' => '1',
        'tenant_lease_expiry_sms' => '1'
    ];

    foreach ($smsSettings as $key => $value) {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Added: $key = $value\n";
    }

    // 3. Add missing SMS templates
    echo "\n3. Adding SMS Templates:\n";
    $smsTemplates = [
        'template_owner_welcome_sms' => json_encode([
            'subject' => 'Welcome to HRMS',
            'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
        ]),
        'template_welcome_sms' => json_encode([
            'subject' => 'Welcome to HRMS',
            'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created.'
        ]),
        'template_otp_verification_sms' => json_encode([
            'subject' => 'OTP Verification',
            'content' => 'Your OTP is {{otp}}. Please use this code to verify your account.'
        ]),
        'template_payment_confirmation_sms' => json_encode([
            'subject' => 'Payment Confirmation',
            'content' => 'Payment of ৳{{amount}} has been confirmed. Transaction ID: {{transaction_id}}'
        ]),
        'template_invoice_reminder_sms' => json_encode([
            'subject' => 'Invoice Reminder',
            'content' => 'Invoice {{invoice_number}} for ৳{{amount}} is due on {{due_date}}. Please make payment.'
        ]),
        'template_subscription_activation_sms' => json_encode([
            'subject' => 'Subscription Activated',
            'content' => 'Your {{plan_name}} subscription has been activated. Valid until {{expiry_date}}.'
        ])
    ];

    foreach ($smsTemplates as $key => $value) {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Added: $key\n";
    }

    // 4. Add missing email templates
    echo "\n4. Adding Email Templates:\n";
    $emailTemplates = [
        'template_welcome_email' => json_encode([
            'subject' => 'Welcome to HRMS',
            'content' => '<h2>Welcome {{name}}!</h2><p>Your account has been successfully created.</p>'
        ]),
        'template_account_setup_guide_email' => json_encode([
            'subject' => 'Account Setup Guide',
            'content' => '<h2>Account Setup Guide</h2><p>Here\'s how to get started with HRMS...</p>'
        ]),
        'template_features_overview_email' => json_encode([
            'subject' => 'HRMS Features Overview',
            'content' => '<h2>Features Overview</h2><p>Discover all the features available in HRMS...</p>'
        ]),
        'template_subscription_info_email' => json_encode([
            'subject' => 'Subscription Information',
            'content' => '<h2>Subscription Details</h2><p>Plan: {{plan_name}}, Price: ৳{{plan_price}}</p>'
        ]),
        'template_payment_success_email' => json_encode([
            'subject' => 'Payment Successful',
            'content' => '<h2>Payment Confirmed</h2><p>Amount: ৳{{amount}}, Transaction: {{transaction_id}}</p>'
        ]),
        'template_invoice_reminder_email' => json_encode([
            'subject' => 'Invoice Reminder',
            'content' => '<h2>Invoice Due</h2><p>Invoice {{invoice_number}} for ৳{{amount}} is due on {{due_date}}</p>'
        ]),
        'template_subscription_expiry_reminder_email' => json_encode([
            'subject' => 'Subscription Expiry Reminder',
            'content' => '<h2>Subscription Expiring</h2><p>Your subscription expires in {{days_left}} days.</p>'
        ]),
        'template_payment_success_email' => json_encode([
            'subject' => 'Payment Success',
            'content' => '<h2>Payment Successful</h2><p>Thank you for your payment of ৳{{amount}}</p>'
        ]),
        'template_security_alert_email' => json_encode([
            'subject' => 'Security Alert',
            'content' => '<h2>Security Alert</h2><p>Activity detected: {{activity}} at {{timestamp}}</p>'
        ]),
        'template_account_verification_email' => json_encode([
            'subject' => 'Account Verification',
            'content' => '<h2>Verify Your Account</h2><p>Click here to verify: {{verification_url}}</p>'
        ]),
        'template_password_reset_email' => json_encode([
            'subject' => 'Password Reset',
            'content' => '<h2>Password Reset</h2><p>Click here to reset: {{reset_url}}</p>'
        ])
    ];

    foreach ($emailTemplates as $key => $value) {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Added: $key\n";
    }

    // 5. Show final count
    echo "\n5. Final Settings Count: " . Setting::count() . "\n";
    
    // 6. Show all settings
    echo "\n6. All Settings:\n";
    $allSettings = Setting::orderBy('key')->get();
    foreach ($allSettings as $setting) {
        echo "- {$setting->key}: " . (is_string($setting->value) ? $setting->value : json_encode($setting->value)) . "\n";
    }

    echo "\n✅ Settings database fixed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
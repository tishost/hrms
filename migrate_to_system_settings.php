<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use App\Models\SystemSetting;

try {
    echo "=== Migrating Settings to SystemSettings ===\n\n";

    // 1. Get all settings from settings table
    $settings = Setting::all();
    echo "Found " . $settings->count() . " settings in settings table\n\n";

    // 2. Migrate each setting to system_settings table
    $migrated = 0;
    $skipped = 0;

    foreach ($settings as $setting) {
        // Check if setting already exists in system_settings
        $existing = SystemSetting::where('key', $setting->key)->first();
        
        if ($existing) {
            echo "⚠️  Skipped: {$setting->key} (already exists)\n";
            $skipped++;
        } else {
            // Create in system_settings table
            SystemSetting::create([
                'key' => $setting->key,
                'value' => $setting->value
            ]);
            echo "✅ Migrated: {$setting->key}\n";
            $migrated++;
        }
    }

    // 3. Add missing SMS settings
    echo "\n3. Adding missing SMS settings:\n";
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
        SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Added: $key = $value\n";
    }

    // 4. Add missing templates
    echo "\n4. Adding missing templates:\n";
    $templates = [
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
        ])
    ];

    foreach ($templates as $key => $value) {
        SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        echo "✅ Added: $key\n";
    }

    // 5. Show final statistics
    echo "\n=== Migration Summary ===\n";
    echo "Migrated: $migrated settings\n";
    echo "Skipped: $skipped settings (already existed)\n";
    echo "Total system_settings: " . SystemSetting::count() . "\n";

    // 6. Show all system settings
    echo "\n=== All System Settings ===\n";
    $allSettings = SystemSetting::orderBy('key')->get();
    foreach ($allSettings as $setting) {
        echo "- {$setting->key}: {$setting->value}\n";
    }

    echo "\n✅ Migration completed successfully!\n";
    echo "All settings are now in system_settings table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
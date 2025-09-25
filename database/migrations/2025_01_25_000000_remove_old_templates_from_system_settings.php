<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // List of old template keys to remove from system_settings
        $oldTemplateKeys = [
            // Email templates
            'welcome_email',
            'account_setup_guide_email',
            'features_overview_email',
            'subscription_info_email',
            'account_verification_email',
            'security_alert_email',
            'subscription_expiry_reminder_email',
            'payment_success_email',
            'invoice_reminder_email',
            'payment_confirmation_email',
            'invoice_notification_email',
            'subscription_reminder_email',
            'subscription_activation_email',

            // SMS templates
            'welcome_sms',
            'owner_welcome_sms',
            'payment_confirmation_sms',
            'due_date_reminder_sms',
            'subscription_activation_sms',
            'invoice_reminder_sms',
            'password_reset_otp_sms',
            'system_otp_sms',

            // Old template format keys (if any exist)
            'template_welcome_email',
            'template_account_setup_guide_email',
            'template_features_overview_email',
            'template_subscription_info_email',
            'template_account_verification_email',
            'template_security_alert_email',
            'template_subscription_expiry_reminder_email',
            'template_payment_success_email',
            'template_invoice_reminder_email',
            'template_payment_confirmation_email',
            'template_invoice_notification_email',
            'template_subscription_reminder_email',
            'template_subscription_activation_email',
            'template_welcome_sms',
            'template_owner_welcome_sms',
            'template_payment_confirmation_sms',
            'template_due_date_reminder_sms',
            'template_subscription_activation_sms',
            'template_invoice_reminder_sms',
            'template_password_reset_otp_sms',
            'template_system_otp_sms',

            // Language-specific template keys (if any exist)
            'welcome_email_content_bangla',
            'welcome_email_content_english',
            'welcome_email_subject_bangla',
            'welcome_email_subject_english',
            'password_reset_email_content_bangla',
            'password_reset_email_content_english',
            'password_reset_email_subject_bangla',
            'password_reset_email_subject_english',
            'password_reset_otp_sms_content_bangla',
            'password_reset_otp_sms_content_english',
        ];

        // Remove old template keys from system_settings
        $removedCount = 0;
        foreach ($oldTemplateKeys as $key) {
            $deleted = DB::table('system_settings')->where('key', $key)->delete();
            if ($deleted > 0) {
                $removedCount += $deleted;
                echo "Removed: {$key}\n";
            }
        }

        echo "Total removed template keys: {$removedCount}\n";

        // Log the cleanup
        \Log::info("Template cleanup completed. Removed {$removedCount} old template keys from system_settings table.");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we're removing data
        // If you need to restore, you would need to re-run the seeders
        echo "This migration cannot be reversed. Use seeders to restore template data.\n";
    }
};

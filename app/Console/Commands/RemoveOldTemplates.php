<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use App\Models\EmailTemplate;
use App\Models\SmsTemplate;

class RemoveOldTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:remove-old {--dry-run : Show what would be removed without actually removing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old template keys from system_settings table after migration to new template tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No data will be removed');
        } else {
            $this->info('🗑️  Removing old template keys from system_settings table...');
        }

        // List of old template keys to remove
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
            
            // Old template format keys
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
            
            // Language-specific template keys
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

        $removedCount = 0;
        $foundKeys = [];
        $notFoundKeys = [];

        $this->info('Checking for old template keys...');
        $this->newLine();

        foreach ($oldTemplateKeys as $key) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if ($setting) {
                $foundKeys[] = $key;
                $this->line("✅ Found: {$key}");
                
                if (!$isDryRun) {
                    $setting->delete();
                    $removedCount++;
                }
            } else {
                $notFoundKeys[] = $key;
            }
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->info("📊 DRY RUN RESULTS:");
            $this->line("Found keys: " . count($foundKeys));
            $this->line("Not found keys: " . count($notFoundKeys));
            
            if (count($foundKeys) > 0) {
                $this->newLine();
                $this->info("Keys that would be removed:");
                foreach ($foundKeys as $key) {
                    $this->line("  - {$key}");
                }
            }
            
            $this->newLine();
            $this->warn("To actually remove these keys, run: php artisan templates:remove-old");
        } else {
            $this->info("✅ Cleanup completed!");
            $this->line("Removed keys: {$removedCount}");
            $this->line("Not found keys: " . count($notFoundKeys));
            
            // Verify new template tables have data
            $emailTemplateCount = EmailTemplate::count();
            $smsTemplateCount = SmsTemplate::count();
            
            $this->newLine();
            $this->info("📊 New template tables status:");
            $this->line("Email templates: {$emailTemplateCount}");
            $this->line("SMS templates: {$smsTemplateCount}");
            
            if ($emailTemplateCount > 0 && $smsTemplateCount > 0) {
                $this->info("✅ Template migration successful!");
            } else {
                $this->warn("⚠️  New template tables appear to be empty. Run seeders first:");
                $this->line("php artisan db:seed --class=EmailTemplateSeeder");
                $this->line("php artisan db:seed --class=SmsTemplateSeeder");
            }
        }

        return 0;
    }
}
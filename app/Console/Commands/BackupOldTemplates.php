<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;

class BackupOldTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:backup-old {--format=json : Backup format (json or sql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup old template keys from system_settings table before removing them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $format = $this->option('format');
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        $this->info('💾 Creating backup of old template keys...');

        // List of old template keys to backup
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

        // Get existing template settings
        $templateSettings = SystemSetting::whereIn('key', $oldTemplateKeys)->get();
        
        if ($templateSettings->isEmpty()) {
            $this->warn('⚠️  No old template keys found in system_settings table.');
            return 0;
        }

        $this->info("Found {$templateSettings->count()} template settings to backup.");

        // Create backup data
        $backupData = [
            'backup_date' => now()->toISOString(),
            'total_records' => $templateSettings->count(),
            'templates' => $templateSettings->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at,
                ];
            })->toArray()
        ];

        // Generate filename
        $filename = "old_templates_backup_{$timestamp}.{$format}";
        $filepath = storage_path("app/backups/{$filename}");

        // Ensure backup directory exists
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        // Save backup based on format
        if ($format === 'json') {
            $content = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($filepath, $content);
        } elseif ($format === 'sql') {
            $content = "-- Old Templates Backup - {$timestamp}\n";
            $content .= "-- Total records: {$templateSettings->count()}\n\n";
            
            foreach ($templateSettings as $setting) {
                $key = addslashes($setting->key);
                $value = addslashes($setting->value);
                $createdAt = $setting->created_at->format('Y-m-d H:i:s');
                $updatedAt = $setting->updated_at->format('Y-m-d H:i:s');
                
                $content .= "INSERT INTO system_settings (id, key, value, created_at, updated_at) VALUES ";
                $content .= "({$setting->id}, '{$key}', '{$value}', '{$createdAt}', '{$updatedAt}');\n";
            }
            
            file_put_contents($filepath, $content);
        } else {
            $this->error("Invalid format: {$format}. Use 'json' or 'sql'.");
            return 1;
        }

        $this->info("✅ Backup created successfully!");
        $this->line("File: {$filepath}");
        $this->line("Format: {$format}");
        $this->line("Records: {$templateSettings->count()}");

        // Show summary
        $this->newLine();
        $this->info("📊 Backup Summary:");
        
        $emailTemplates = $templateSettings->filter(function ($setting) {
            return strpos($setting->key, 'email') !== false;
        })->count();
        
        $smsTemplates = $templateSettings->filter(function ($setting) {
            return strpos($setting->key, 'sms') !== false;
        })->count();
        
        $this->line("Email templates: {$emailTemplates}");
        $this->line("SMS templates: {$smsTemplates}");
        $this->line("Other templates: " . ($templateSettings->count() - $emailTemplates - $smsTemplates));

        $this->newLine();
        $this->info("💡 Next steps:");
        $this->line("1. Verify the backup file");
        $this->line("2. Run: php artisan templates:remove-old --dry-run");
        $this->line("3. Run: php artisan templates:remove-old");

        return 0;
    }
}
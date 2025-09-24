<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\SystemSetting;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all email template settings from system_settings
        $emailTemplates = [
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
            'subscription_activation_email'
        ];

        foreach ($emailTemplates as $templateName) {
            $this->migrateEmailTemplate($templateName);
        }

        $this->command->info('Email templates migrated successfully!');
    }

    private function migrateEmailTemplate($templateName)
    {
        // Get template data from system_settings
        $subjectBangla = SystemSetting::getValue($templateName . '_subject_bangla');
        $subjectEnglish = SystemSetting::getValue($templateName . '_subject_english');
        $contentBangla = SystemSetting::getValue($templateName . '_content_bangla');
        $contentEnglish = SystemSetting::getValue($templateName . '_content_english');

        // Skip if no data exists
        if (!$subjectBangla && !$subjectEnglish && !$contentBangla && !$contentEnglish) {
            $this->command->warn("No data found for template: {$templateName}");
            return;
        }

        // Determine category
        $category = $this->getTemplateCategory($templateName);

        // Extract variables from content
        $variables = $this->extractVariables($contentBangla . ' ' . $contentEnglish);

        // Create email template
        EmailTemplate::updateOrCreate(
            ['name' => $templateName],
            [
                'subject_bangla' => $subjectBangla,
                'subject_english' => $subjectEnglish,
                'content_bangla' => $contentBangla,
                'content_english' => $contentEnglish,
                'variables' => $variables,
                'category' => $category,
                'is_active' => true,
                'description' => $this->getTemplateDescription($templateName),
                'priority' => $this->getTemplatePriority($templateName),
                'tags' => $this->getTemplateTags($templateName)
            ]
        );

        $this->command->info("Migrated template: {$templateName}");
    }

    private function getTemplateCategory($templateName)
    {
        if (strpos($templateName, 'owner_') === 0 || strpos($templateName, 'subscription_') === 0) {
            return 'owner';
        } elseif (strpos($templateName, 'tenant_') === 0) {
            return 'tenant';
        }
        return 'system';
    }

    private function extractVariables($content)
    {
        preg_match_all('/\{([^}]+)\}/', $content, $matches);
        return array_unique($matches[1] ?? []);
    }

    private function getTemplateDescription($templateName)
    {
        $descriptions = [
            'welcome_email' => 'Welcome email sent to new users',
            'account_setup_guide_email' => 'Account setup guide email for new users',
            'features_overview_email' => 'Features overview email for users',
            'subscription_info_email' => 'Subscription information email for owners',
            'account_verification_email' => 'Account verification email',
            'security_alert_email' => 'Security alert email',
            'subscription_expiry_reminder_email' => 'Subscription expiry reminder email',
            'payment_success_email' => 'Payment success confirmation email',
            'invoice_reminder_email' => 'Invoice reminder email',
            'payment_confirmation_email' => 'Payment confirmation email',
            'invoice_notification_email' => 'Invoice notification email',
            'subscription_reminder_email' => 'Subscription reminder email',
            'subscription_activation_email' => 'Subscription activation email'
        ];

        return $descriptions[$templateName] ?? 'Email template';
    }

    private function getTemplatePriority($templateName)
    {
        $priorities = [
            'welcome_email' => 1,
            'account_verification_email' => 1,
            'security_alert_email' => 1,
            'payment_success_email' => 2,
            'subscription_expiry_reminder_email' => 2,
            'invoice_reminder_email' => 3,
            'payment_confirmation_email' => 3,
            'invoice_notification_email' => 3,
            'subscription_reminder_email' => 3,
            'subscription_activation_email' => 3,
            'account_setup_guide_email' => 4,
            'features_overview_email' => 4,
            'subscription_info_email' => 4
        ];

        return $priorities[$templateName] ?? 5;
    }

    private function getTemplateTags($templateName)
    {
        $tags = [];

        if (strpos($templateName, 'welcome') !== false) {
            $tags[] = 'welcome';
        }
        if (strpos($templateName, 'payment') !== false) {
            $tags[] = 'payment';
        }
        if (strpos($templateName, 'subscription') !== false) {
            $tags[] = 'subscription';
        }
        if (strpos($templateName, 'invoice') !== false) {
            $tags[] = 'invoice';
        }
        if (strpos($templateName, 'security') !== false) {
            $tags[] = 'security';
        }
        if (strpos($templateName, 'verification') !== false) {
            $tags[] = 'verification';
        }

        return $tags;
    }
}

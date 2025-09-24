<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmsTemplate;
use App\Models\SystemSetting;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all SMS template settings from system_settings
        $smsTemplates = [
            'welcome_sms',
            'owner_welcome_sms',
            'payment_confirmation_sms',
            'due_date_reminder_sms',
            'subscription_activation_sms',
            'invoice_reminder_sms',
            'password_reset_otp_sms',
            'system_otp_sms'
        ];

        foreach ($smsTemplates as $templateName) {
            $this->migrateSmsTemplate($templateName);
        }

        $this->command->info('SMS templates migrated successfully!');
    }

    private function migrateSmsTemplate($templateName)
    {
        // Get template data from system_settings
        $contentBangla = SystemSetting::getValue($templateName . '_content_bangla');
        $contentEnglish = SystemSetting::getValue($templateName . '_content_english');

        // Skip if no data exists
        if (!$contentBangla && !$contentEnglish) {
            $this->command->warn("No data found for SMS template: {$templateName}");
            return;
        }

        // Determine category
        $category = $this->getTemplateCategory($templateName);

        // Extract variables from content
        $variables = $this->extractVariables($contentBangla . ' ' . $contentEnglish);

        // Determine character limit based on content
        $characterLimit = $this->getCharacterLimit($templateName, $contentBangla, $contentEnglish);

        // Create SMS template
        SmsTemplate::updateOrCreate(
            ['name' => $templateName],
            [
                'content_bangla' => $contentBangla,
                'content_english' => $contentEnglish,
                'variables' => $variables,
                'category' => $category,
                'is_active' => true,
                'description' => $this->getTemplateDescription($templateName),
                'character_limit' => $characterLimit,
                'priority' => $this->getTemplatePriority($templateName),
                'tags' => $this->getTemplateTags($templateName),
                'unicode_support' => $this->hasUnicodeSupport($contentBangla)
            ]
        );

        $this->command->info("Migrated SMS template: {$templateName}");
    }

    private function getTemplateCategory($templateName)
    {
        if (strpos($templateName, 'owner_') === 0) {
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

    private function getCharacterLimit($templateName, $contentBangla, $contentEnglish)
    {
        // Check if content has Bengali characters (Unicode)
        $hasUnicode = $this->hasUnicodeSupport($contentBangla);

        if ($hasUnicode) {
            // Unicode SMS typically has 70 character limit
            return 70;
        }

        // Regular SMS has 160 character limit
        return 160;
    }

    private function hasUnicodeSupport($content)
    {
        if (!$content) return false;

        // Check for Bengali characters (Unicode range: U+0980-U+09FF)
        return preg_match('/[\x{0980}-\x{09FF}]/u', $content);
    }

    private function getTemplateDescription($templateName)
    {
        $descriptions = [
            'welcome_sms' => 'Welcome SMS for new users',
            'owner_welcome_sms' => 'Welcome SMS for new owners',
            'payment_confirmation_sms' => 'Payment confirmation SMS',
            'due_date_reminder_sms' => 'Due date reminder SMS',
            'subscription_activation_sms' => 'Subscription activation SMS',
            'invoice_reminder_sms' => 'Invoice reminder SMS',
            'password_reset_otp_sms' => 'Password reset OTP SMS',
            'system_otp_sms' => 'System OTP verification SMS'
        ];

        return $descriptions[$templateName] ?? 'SMS template';
    }

    private function getTemplatePriority($templateName)
    {
        $priorities = [
            'password_reset_otp_sms' => 1,
            'system_otp_sms' => 1,
            'welcome_sms' => 2,
            'owner_welcome_sms' => 2,
            'payment_confirmation_sms' => 3,
            'subscription_activation_sms' => 3,
            'due_date_reminder_sms' => 4,
            'invoice_reminder_sms' => 4
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
        if (strpos($templateName, 'otp') !== false) {
            $tags[] = 'otp';
        }
        if (strpos($templateName, 'reminder') !== false) {
            $tags[] = 'reminder';
        }
        if (strpos($templateName, 'owner') !== false) {
            $tags[] = 'owner';
        }

        return $tags;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OwnerSetting extends Model
{
    protected $fillable = [
        'owner_id',
        'key',
        'value',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get setting value by key
     */
    public static function getValue($ownerId, $key, $default = null)
    {
        $setting = self::where('owner_id', $ownerId)
                       ->where('key', $key)
                       ->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($ownerId, $key, $value)
    {
        return self::updateOrCreate(
            ['owner_id' => $ownerId, 'key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get multiple settings for an owner
     */
    public static function getSettings($ownerId)
    {
        return self::where('owner_id', $ownerId)
                   ->pluck('value', 'key')
                   ->toArray();
    }

    /**
     * Set multiple settings for an owner
     */
    public static function setSettings($ownerId, $settings)
    {
        foreach ($settings as $key => $value) {
            self::setValue($ownerId, $key, $value);
        }
    }

    /**
     * Get admin template as default
     */
    public static function getAdminTemplate($templateKey)
    {
        $adminTemplate = SystemSetting::getValue('template_' . $templateKey);
        
        if ($adminTemplate) {
            $templateData = json_decode($adminTemplate, true);
            return $templateData['content'] ?? '';
        }
        
        return '';
    }

    /**
     * Get template with fallback to admin template
     */
    public static function getTemplateWithFallback($ownerId, $templateKey)
    {
        // First try to get owner's custom template
        $ownerTemplate = self::getValue($ownerId, $templateKey);
        
        if ($ownerTemplate) {
            return $ownerTemplate;
        }
        
        // If no owner template, get admin template
        return self::getAdminTemplate($templateKey);
    }

    /**
     * Get template with language preference
     */
    public static function getTemplateWithLanguage($ownerId, $templateKey, $data = [])
    {
        $language = self::getValue($ownerId, 'notification_language', 'bangla');
        
        // Get template based on language
        $template = self::getTemplateWithFallback($ownerId, $templateKey . '_' . $language);
        
        // If language-specific template doesn't exist, fall back to default
        if (!$template) {
            $template = self::getTemplateWithFallback($ownerId, $templateKey);
        }
        
        if (!$template) {
            return null;
        }

        // Replace placeholders
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    /**
     * Get all templates for owner settings page (with admin defaults and language support)
     */
    public static function getTemplatesForOwner($ownerId)
    {
        $ownerSettings = self::getSettings($ownerId);
        
        // Define all template keys with language support
        $templateKeys = [
            // SMS Templates
            'tenant_welcome_sms_template_bangla',
            'rent_due_sms_template_bangla', 
            'rent_paid_sms_template_bangla',
            'checkout_sms_template_bangla',
            
            'tenant_welcome_sms_template_english',
            'rent_due_sms_template_english', 
            'rent_paid_sms_template_english',
            'checkout_sms_template_english',
            
            // Email Templates
            'tenant_welcome_email_template_bangla',
            'rent_due_email_template_bangla',
            'rent_paid_email_template_bangla',
            'checkout_email_template_bangla',
            'lease_expiry_email_template_bangla',
            
            'tenant_welcome_email_template_english',
            'rent_due_email_template_english',
            'rent_paid_email_template_english',
            'checkout_email_template_english',
            'lease_expiry_email_template_english',
        ];
        
        $templates = [];
        
        foreach ($templateKeys as $key) {
            // If owner has custom template, use it
            if (isset($ownerSettings[$key])) {
                $templates[$key] = $ownerSettings[$key];
            } else {
                // Otherwise use admin template with language
                $adminKey = str_replace('_template', '', $key);
                $template = self::getAdminTemplate($adminKey);
                
                // If no admin template, provide default
                if (!$template) {
                    $templates[$key] = self::getDefaultTemplate($key);
                } else {
                    $templates[$key] = $template;
                }
            }
        }
        
        return $templates;
    }

    /**
     * Get default template content
     */
    public static function getDefaultTemplate($templateKey)
    {
        $defaults = [
            // Bangla SMS Templates
            'tenant_welcome_sms_template_bangla' => 'স্বাগতম {tenant_name}! আপনার ইউনিট {unit_name} প্রস্তুত।',
            'rent_due_sms_template_bangla' => 'প্রিয় {tenant_name}, {month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি।',
            'rent_paid_sms_template_bangla' => 'ধন্যবাদ {tenant_name}! {month} মাসের ভাড়া ৳{amount} পাওয়া গেছে।',
            'checkout_sms_template_bangla' => 'প্রিয় {tenant_name}, {unit_name} এর চেকআউট সম্পন্ন।',
            
            // English SMS Templates
            'tenant_welcome_sms_template_english' => 'Welcome {tenant_name}! Your unit {unit_name} at {property_name} is ready.',
            'rent_due_sms_template_english' => 'Dear {tenant_name}, rent of ৳{amount} for {month} is due on {due_date}.',
            'rent_paid_sms_template_english' => 'Thank you {tenant_name}! Rent payment of ৳{amount} for {month} received.',
            'checkout_sms_template_english' => 'Dear {tenant_name}, checkout process completed for {unit_name}.',
            
            // Bangla Email Templates
            'tenant_welcome_email_template_bangla' => 'স্বাগতম {tenant_name}! আপনার ইউনিট {unit_name} প্রস্তুত।',
            'rent_due_email_template_bangla' => 'প্রিয় {tenant_name}, {month} মাসের ভাড়া ৳{amount} {due_date} তারিখে বাকি।',
            'rent_paid_email_template_bangla' => 'ধন্যবাদ {tenant_name}! {month} মাসের ভাড়া ৳{amount} পাওয়া গেছে।',
            'checkout_email_template_bangla' => 'প্রিয় {tenant_name}, {unit_name} এর চেকআউট সম্পন্ন।',
            'lease_expiry_email_template_bangla' => 'প্রিয় {tenant_name}, আপনার {unit_name} এর লিজ {expiry_date} তারিখে শেষ।',
            
            // English Email Templates
            'tenant_welcome_email_template_english' => 'Welcome {tenant_name}! Your unit {unit_name} at {property_name} is ready.',
            'rent_due_email_template_english' => 'Dear {tenant_name}, rent of ৳{amount} for {month} is due on {due_date}.',
            'rent_paid_email_template_english' => 'Thank you {tenant_name}! Rent payment of ৳{amount} for {month} received.',
            'checkout_email_template_english' => 'Dear {tenant_name}, checkout process completed for {unit_name}.',
            'lease_expiry_email_template_english' => 'Dear {tenant_name}, your lease for {unit_name} expires on {expiry_date}.',
        ];
        
        return $defaults[$templateKey] ?? '';
    }

    /**
     * Get default settings (only for non-template settings)
     */
    public static function getDefaultSettings()
    {
        return [
            // Language Settings
            'notification_language' => 'bangla', // 'bangla' or 'english'
            
            // Notification Settings
            'notify_rent_due' => '1',
            'notify_rent_paid' => '1',
            'notify_new_tenant' => '1',
            'notify_checkout' => '1',
            'notify_late_payment' => '1',
            'auto_send_reminders' => '1',
            
            // Reminder Settings
            'rent_due_reminder_days' => '7',
            'late_payment_reminder_days' => '3',
            
            // System Settings
            'currency_symbol' => '৳',
            'date_format' => 'Y-m-d',
            'timezone' => 'Asia/Dhaka',
            'maintenance_mode' => '0',
        ];
    }

    /**
     * Initialize default settings for an owner
     */
    public static function initializeDefaults($ownerId)
    {
        $defaults = self::getDefaultSettings();
        foreach ($defaults as $key => $value) {
            self::setValue($ownerId, $key, $value);
        }
    }

    /**
     * Get template with placeholders replaced
     */
    public static function getTemplate($ownerId, $templateKey, $data = [])
    {
        $template = self::getTemplateWithFallback($ownerId, $templateKey);
        
        if (!$template) {
            return null;
        }

        // Replace placeholders
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    /**
     * Check if notification is enabled
     */
    public static function isNotificationEnabled($ownerId, $notificationType)
    {
        return self::getValue($ownerId, 'notify_' . $notificationType, '1') == '1';
    }

    /**
     * Get system setting
     */
    public static function getSystemSetting($ownerId, $key, $default = null)
    {
        return self::getValue($ownerId, $key, $default);
    }

    /**
     * Optimized template retrieval with single query
     */
    public static function getTemplateOptimized($ownerId, $templateKey, $data = [])
    {
        // Get all owner settings in one optimized query
        $ownerSettings = self::where('owner_id', $ownerId)
                            ->pluck('value', 'key')
                            ->toArray();
        
        $language = $ownerSettings['notification_language'] ?? 'bangla';
        
        // Try language-specific template first
        $langKey = $templateKey . '_' . $language;
        $template = $ownerSettings[$langKey] ?? null;
        
        // If no language-specific template, try default
        if (!$template) {
            $template = $ownerSettings[$templateKey] ?? null;
        }
        
        // If no owner template, get admin template
        if (!$template) {
            $template = self::getAdminTemplate($templateKey);
        }
        
        // If no admin template, get system default
        if (!$template) {
            $template = self::getDefaultTemplate($templateKey);
        }
        
        // Replace placeholders if template exists
        if ($template) {
            foreach ($data as $key => $value) {
                $template = str_replace('{' . $key . '}', $value, $template);
            }
        }
        
        return $template;
    }

    /**
     * Batch template retrieval for multiple templates (optimized)
     */
    public static function getTemplatesBatch($ownerId, $templateKeys, $data = [])
    {
        // Single query to get all owner settings
        $ownerSettings = self::where('owner_id', $ownerId)
                            ->pluck('value', 'key')
                            ->toArray();
        
        $language = $ownerSettings['notification_language'] ?? 'bangla';
        
        $templates = [];
        
        foreach ($templateKeys as $templateKey) {
            // Try language-specific template first
            $langKey = $templateKey . '_' . $language;
            $template = $ownerSettings[$langKey] ?? null;
            
            // If no language-specific template, try default
            if (!$template) {
                $template = $ownerSettings[$templateKey] ?? null;
            }
            
            // If no owner template, get admin template
            if (!$template) {
                $template = self::getAdminTemplate($templateKey);
            }
            
            // If no admin template, get system default
            if (!$template) {
                $template = self::getDefaultTemplate($templateKey);
            }
            
            $templates[$templateKey] = $template;
        }
        
        return $templates;
    }

    /**
     * Pre-load all templates for an owner (for high-frequency usage)
     */
    public static function preloadOwnerTemplates($ownerId)
    {
        static $preloadedTemplates = [];
        
        // Return cached templates if already loaded
        if (isset($preloadedTemplates[$ownerId])) {
            return $preloadedTemplates[$ownerId];
        }
        
        // Get all owner settings in one query
        $ownerSettings = self::where('owner_id', $ownerId)
                            ->pluck('value', 'key')
                            ->toArray();
        
        $language = $ownerSettings['notification_language'] ?? 'bangla';
        
        // Pre-load all possible templates
        $templateKeys = [
            'tenant_welcome_sms_template',
            'rent_due_sms_template',
            'rent_paid_sms_template',
            'checkout_sms_template',
            'tenant_welcome_email_template',
            'rent_due_email_template',
            'rent_paid_email_template',
            'checkout_email_template',
            'lease_expiry_email_template',
        ];
        
        $templates = [];
        
        foreach ($templateKeys as $templateKey) {
            // Try language-specific template first
            $langKey = $templateKey . '_' . $language;
            $template = $ownerSettings[$langKey] ?? null;
            
            // If no language-specific template, try default
            if (!$template) {
                $template = $ownerSettings[$templateKey] ?? null;
            }
            
            // If no owner template, get admin template
            if (!$template) {
                $template = self::getAdminTemplate($templateKey);
            }
            
            // If no admin template, get system default
            if (!$template) {
                $template = self::getDefaultTemplate($templateKey);
            }
            
            $templates[$templateKey] = $template;
        }
        
        // Cache in memory for this request
        $preloadedTemplates[$ownerId] = $templates;
        
        return $templates;
    }

    /**
     * Get template from pre-loaded data (ultra-fast)
     */
    public static function getTemplateFromPreloaded($ownerId, $templateKey, $data = [])
    {
        $templates = self::preloadOwnerTemplates($ownerId);
        $template = $templates[$templateKey] ?? null;
        
        // Replace placeholders if template exists
        if ($template) {
            foreach ($data as $key => $value) {
                $template = str_replace('{' . $key . '}', $value, $template);
            }
        }
        
        return $template;
    }
}

<?php

namespace App\Helpers;

use App\Services\NotificationService;
use App\Helpers\SmsHelper;

/**
 * 🚀 HRMS Notification Helper
 * 
 * IMPORTANT DEVELOPMENT INSTRUCTIONS TO REMEMBER:
 * 
 * 1. ✅ সব notification এই Helper দিয়ে পাঠাবো (SMS, Email, OTP, App notifications)
 * 2. ✅ SMS এর জন্য SmsHelper ব্যবহার করবো
 * 3. ✅ Multiple function/controller তৈরি করবো না
 * 4. ✅ Template-based notifications ব্যবহার করবো
 * 5. ✅ Existing functions reuse করবো
 * 6. ✅ Error handling করবো
 * 
 * PATTERNS TO FOLLOW:
 * - SMS: NotificationHelper::sendOtpSms($phone, $otp)
 * - Email: NotificationHelper::sendPasswordResetEmail($user, $token)
 * - Template: NotificationHelper::sendTemplate('sms', $phone, 'template_name', $variables)
 * 
 * DON'T DO:
 * - ❌ Direct SMS service calls
 * - ❌ Hard-coded messages
 * - ❌ Duplicate functions
 * - ❌ New controllers for same purpose
 * 
 * REMEMBER: Always use this helper for all notifications!
 */
class NotificationHelper
{
    private static $notificationService = null;

    /**
     * Get notification service instance
     */
    private static function getService()
    {
        if (self::$notificationService === null) {
            self::$notificationService = new NotificationService();
        }
        return self::$notificationService;
    }

    /**
     * Send SMS notification
     */
    public static function sendSms($to, $message, $variables = [])
    {
        return self::getService()->sendSms($to, $message, null, $variables);
    }

    /**
     * Send Email notification
     */
    public static function sendEmail($to, $subject, $content, $variables = [])
    {
        return self::getService()->sendEmail($to, $subject, $content, null, $variables);
    }

    /**
     * Send notification using template
     */
    public static function sendTemplate($type, $to, $templateName, $variables = [])
    {
        return self::getService()->sendTemplateNotification($type, $to, $templateName, $variables);
    }

    /**
     * Send payment confirmation notification
     */
    public static function sendPaymentConfirmation($user, $amount, $invoiceNumber, $paymentMethod)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'payment_method' => $paymentMethod,
        ];

        $results = [];

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'payment_confirmation_email', $variables);

        // Send SMS if phone exists
        if ($user->phone) {
            $results['sms'] = self::sendTemplate('sms', $user->phone, 'payment_confirmation_sms', $variables);
        }

        return $results;
    }

    /**
     * Send invoice notification
     */
    public static function sendInvoiceNotification($user, $amount, $invoiceNumber, $dueDate)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'due_date' => $dueDate,
        ];

        $results = [];

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'invoice_notification_email', $variables);

        // Send SMS if phone exists
        if ($user->phone) {
            $results['sms'] = self::sendTemplate('sms', $user->phone, 'due_date_reminder_sms', $variables);
        }

        return $results;
    }

    /**
     * Send subscription reminder
     */
    public static function sendSubscriptionReminder($user, $planName, $expiryDate)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'plan_name' => $planName,
            'expiry_date' => $expiryDate,
        ];

        $results = [];

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'subscription_reminder_email', $variables);

        return $results;
    }

    /**
     * Send subscription activation notification
     */
    public static function sendSubscriptionActivation($user, $planName, $expiryDate)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'plan_name' => $planName,
            'expiry_date' => $expiryDate->format('M d, Y'),
        ];

        $results = [];

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'subscription_activation_email', $variables);

        // Send SMS if phone exists
        if ($user->phone) {
            $results['sms'] = self::sendTemplate('sms', $user->phone, 'subscription_activation_sms', $variables);
        }

        return $results;
    }

    /**
     * Send welcome notification
     */
    public static function sendWelcomeNotification($user)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $results = [];

        // Send welcome email
        $results['welcome_email'] = self::sendTemplate('email', $user->email, 'welcome_email', $variables);

        // Send account setup guide email
        $results['setup_guide_email'] = self::sendTemplate('email', $user->email, 'account_setup_guide_email', $variables);

        // Send subscription information email (if owner)
        if ($user->owner && $user->owner->subscription) {
            $subscriptionVariables = array_merge($variables, [
                'plan_name' => $user->owner->subscription->plan->name ?? 'Free Plan',
                'plan_price' => $user->owner->subscription->plan->price ?? 0,
                'expiry_date' => $user->owner->subscription->end_date ?? null,
            ]);
            $results['subscription_email'] = self::sendTemplate('email', $user->email, 'subscription_info_email', $subscriptionVariables);
        }

        // Send SMS if phone exists
        if ($user->phone) {
            if ($user->owner) {
                // Check if owner welcome SMS is enabled
                if (self::isSmsNotificationEnabled('owner_welcome_sms')) {
                    // Use system SMS for welcome (no credit deduction)
                    $results['sms'] = self::sendTemplate('sms', $user->phone, 'owner_welcome_sms', $variables);
                }
            } else {
                // Check if system welcome SMS is enabled
                if (self::isSmsNotificationEnabled('system_welcome_sms')) {
                    $results['sms'] = self::sendTemplate('sms', $user->phone, 'welcome_sms', $variables);
                }
            }
        }

        return $results;
    }

    /**
     * Check if specific SMS notification is enabled
     */
    public static function isSmsNotificationEnabled($notificationType)
    {
        $setting = \App\Models\SystemSetting::where('key', $notificationType)->value('value');
        return $setting === null ? true : ($setting === '1');
    }

    /**
     * Send OTP verification SMS
     */
    public static function sendOtpSms($phone, $otp)
    {
        // Check if OTP SMS is enabled
        if (!self::isSmsNotificationEnabled('system_otp_sms')) {
            return ['success' => false, 'message' => 'OTP SMS notifications are disabled'];
        }

        // Get language settings
        $notificationLanguage = \App\Models\SystemSetting::getValue('notification_language', 'bangla');

        // Get template content from new system
        $contentBangla = \App\Models\SystemSetting::getValue('password_reset_otp_sms_content_bangla', 'আপনার OTP: {otp}। এই OTP 10 মিনিটের জন্য বৈধ। {company_name}');
        $contentEnglish = \App\Models\SystemSetting::getValue('password_reset_otp_sms_content_english', 'Your OTP: {otp}. This OTP is valid for 10 minutes. {company_name}');

        // Replace variables in template
        $variables = [
            'otp' => $otp,
            'company_name' => config('app.name', 'HRMS')
        ];

        // Replace variables in content
        $contentBangla = self::replaceVariables($contentBangla, $variables);
        $contentEnglish = self::replaceVariables($contentEnglish, $variables);

        // Determine which language to use
        $content = $contentBangla;

        if ($notificationLanguage === 'english') {
            $content = $contentEnglish;
        } elseif ($notificationLanguage === 'both') {
            // Send both languages (SMS has character limit, so send Bangla first)
            $content = $contentBangla . ' / ' . $contentEnglish;
        }

        // Send SMS with selected language
        return self::sendSms($phone, $content, $variables);
    }

    /**
     * Send account verification email
     */
    public static function sendAccountVerificationEmail($user)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'verification_url' => route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)])
        ];

        return self::sendTemplate('email', $user->email, 'account_verification_email', $variables);
    }

    /**
     * Send password reset email
     */
    public static function sendPasswordResetEmail($user, $resetToken)
    {
        // Get language settings
        $notificationLanguage = \App\Models\SystemSetting::getValue('notification_language', 'bangla');
        $userLanguagePreference = \App\Models\SystemSetting::getValue('user_language_preference', 'enabled');

        // Get template content from new system
        $subjectBangla = \App\Models\SystemSetting::getValue('password_reset_email_subject_bangla', 'পাসওয়ার্ড রিসেট অনুরোধ');
        $contentBangla = \App\Models\SystemSetting::getValue('password_reset_email_content_bangla', 'প্রিয় {user_name}, আপনার পাসওয়ার্ড রিসেট করার অনুরোধ পাওয়া গেছে। আপনার OTP: {otp} এই OTP 10 মিনিটের জন্য বৈধ। যদি আপনি এই অনুরোধ করেননি, তাহলে এই ইমেইল উপেক্ষা করুন। ধন্যবাদ, {company_name}');
        
        $subjectEnglish = \App\Models\SystemSetting::getValue('password_reset_email_subject_english', 'Password Reset Request');
        $contentEnglish = \App\Models\SystemSetting::getValue('password_reset_email_content_english', 'Dear {user_name}, A password reset request has been received for your account. Your OTP: {otp} This OTP is valid for 10 minutes. If you did not request this, please ignore this email. Thank you, {company_name}');

        // Replace variables in template
        $variables = [
            'user_name' => $user->name,
            'email' => $user->email,
            'otp' => $resetToken,
            'company_name' => config('app.name', 'HRMS')
        ];

        // Replace variables in content
        $subjectBangla = self::replaceVariables($subjectBangla, $variables);
        $contentBangla = self::replaceVariables($contentBangla, $variables);
        $subjectEnglish = self::replaceVariables($subjectEnglish, $variables);
        $contentEnglish = self::replaceVariables($contentEnglish, $variables);

        // Determine which language to use
        $subject = $subjectBangla;
        $content = $contentBangla;

        if ($notificationLanguage === 'english') {
            $subject = $subjectEnglish;
            $content = $contentEnglish;
        } elseif ($notificationLanguage === 'both') {
            // Send both languages
            $subject = $subjectBangla . ' / ' . $subjectEnglish;
            $content = $contentBangla . "\n\n---\n\n" . $contentEnglish;
        }

        // Send email with selected language
        return self::sendEmail($user->email, $subject, $content);
    }

    /**
     * Replace variables in template content
     */
    private static function replaceVariables($content, $variables)
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }

    /**
     * Send account security alert email
     */
    public static function sendSecurityAlertEmail($user, $activity)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'activity' => $activity,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'ip_address' => request()->ip()
        ];

        return self::sendTemplate('email', $user->email, 'security_alert_email', $variables);
    }

    /**
     * Send subscription expiry reminder email
     */
    public static function sendSubscriptionExpiryReminder($user, $daysLeft)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'days_left' => $daysLeft,
            'expiry_date' => $user->owner->subscription->end_date ?? null,
            'plan_name' => $user->owner->subscription->plan->name ?? 'Current Plan'
        ];

        return self::sendTemplate('email', $user->email, 'subscription_expiry_reminder_email', $variables);
    }

    /**
     * Send payment success email
     */
    public static function sendPaymentSuccessEmail($user, $paymentDetails)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'amount' => $paymentDetails['amount'],
            'transaction_id' => $paymentDetails['transaction_id'],
            'payment_method' => $paymentDetails['payment_method'],
            'payment_date' => $paymentDetails['payment_date']
        ];

        return self::sendTemplate('email', $user->email, 'payment_success_email', $variables);
    }

    /**
     * Send invoice reminder email
     */
    public static function sendInvoiceReminderEmail($user, $invoiceDetails)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'invoice_number' => $invoiceDetails['invoice_number'],
            'amount' => $invoiceDetails['amount'],
            'due_date' => $invoiceDetails['due_date'],
            'payment_url' => route('owner.subscription.payment', ['invoice_id' => $invoiceDetails['invoice_id']])
        ];

        return self::sendTemplate('email', $user->email, 'invoice_reminder_email', $variables);
    }

    /**
     * Send comprehensive welcome notification (Email + SMS)
     */
    public static function sendComprehensiveWelcome($user)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $results = [];

        // Send multiple welcome emails
        $results['welcome_email'] = self::sendTemplate('email', $user->email, 'welcome_email', $variables);
        $results['setup_guide_email'] = self::sendTemplate('email', $user->email, 'account_setup_guide_email', $variables);
        $results['features_email'] = self::sendTemplate('email', $user->email, 'features_overview_email', $variables);

        // Send subscription info email if owner
        if ($user->owner && $user->owner->subscription) {
            $subscriptionVariables = array_merge($variables, [
                'plan_name' => $user->owner->subscription->plan->name ?? 'Free Plan',
                'plan_price' => $user->owner->subscription->plan->price ?? 0,
                'expiry_date' => $user->owner->subscription->end_date ?? null,
                'sms_credits' => $user->owner->subscription->sms_credits ?? 0,
            ]);
            $results['subscription_email'] = self::sendTemplate('email', $user->email, 'subscription_info_email', $subscriptionVariables);
        }

        // Send SMS if phone exists
        if ($user->phone) {
            // Load owner relationship if not loaded
            if (!$user->relationLoaded('owner')) {
                $user->load('owner');
            }
            
            if ($user->owner) {
                // Check if owner welcome SMS is enabled
                if (self::isSmsNotificationEnabled('owner_welcome_sms')) {
                    // Use system SMS for welcome (no credit deduction)
                    $results['sms'] = self::sendTemplate('sms', $user->phone, 'owner_welcome_sms', $variables);
                }
            } else {
                // Check if system welcome SMS is enabled
                if (self::isSmsNotificationEnabled('system_welcome_sms')) {
                    $results['sms'] = self::sendTemplate('sms', $user->phone, 'welcome_sms', $variables);
                }
            }
        }

        return $results;
    }

    /**
     * Send payment notification (Email + SMS)
     */
    public static function sendPaymentNotification($user, $paymentDetails)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'amount' => $paymentDetails['amount'],
            'transaction_id' => $paymentDetails['transaction_id'],
            'payment_method' => $paymentDetails['payment_method'],
            'payment_date' => $paymentDetails['payment_date']
        ];

        $results = [];

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'payment_success_email', $variables);

        // Send SMS if phone exists
        if ($user->phone) {
            // Use system SMS for payment confirmation (no credit deduction)
            $results['sms'] = self::sendTemplate('sms', $user->phone, 'payment_confirmation_sms', $variables);
        }

        return $results;
    }

    /**
     * Send invoice reminder (Email + SMS)
     */
    public static function sendInvoiceReminder($user, $invoiceDetails)
    {
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'invoice_number' => $invoiceDetails['invoice_number'],
            'amount' => $invoiceDetails['amount'],
            'due_date' => $invoiceDetails['due_date'],
            'payment_url' => route('owner.subscription.payment', ['invoice_id' => $invoiceDetails['invoice_id']])
        ];

        $results = [];

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'invoice_reminder_email', $variables);

        // Send SMS if phone exists
        if ($user->phone) {
            // Use system SMS for invoice reminder (no credit deduction)
            $results['sms'] = self::sendTemplate('sms', $user->phone, 'invoice_reminder_sms', $variables);
        }

        return $results;
    }

    /**
     * Send multiple notifications
     */
    public static function sendMultiple($notifications)
    {
        return self::getService()->sendMultiple($notifications);
    }

    /**
     * Get SMS statistics
     */
    public static function getSmsStats()
    {
        return self::getService()->getSmsStats();
    }

    /**
     * Enable/Disable SMS
     */
    public static function setSmsEnabled($enabled)
    {
        return self::getService()->setSmsEnabled($enabled);
    }

    /**
     * Enable/Disable Email
     */
    public static function setEmailEnabled($enabled)
    {
        return self::getService()->setEmailEnabled($enabled);
    }

    /**
     * Set SMS monthly limit
     */
    public static function setSmsLimit($limit)
    {
        return self::getService()->setSmsLimit($limit);
    }

    /**
     * Reset SMS count
     */
    public static function resetSmsCount()
    {
        return self::getService()->resetSmsCount();
    }

    /**
     * Test SMS sending
     */
    public static function testSms($phone)
    {
        return self::getService()->testSms($phone);
    }

    /**
     * Test Email sending
     */
    public static function testEmail($email)
    {
        return self::getService()->testEmail($email);
    }

    /**
     * Send notification with owner's language preference
     */
    public static function sendNotificationWithLanguage($ownerId, $type, $recipient, $templateKey, $variables = [])
    {
        // Get owner's language preference
        $language = \App\Models\OwnerSetting::getValue($ownerId, 'notification_language', 'bangla');
        
        // Get template with language preference
        $template = \App\Models\OwnerSetting::getTemplateWithLanguage($ownerId, $templateKey, $variables);
        
        if (!$template) {
            // Fallback to system template
            $template = self::getSystemTemplate($templateKey . '_' . $language);
            if (!$template) {
                $template = self::getSystemTemplate($templateKey);
            }
            
            if (!$template) {
                return false;
            }
            
            // Replace variables in system template
            foreach ($variables as $key => $value) {
                $template = str_replace('{' . $key . '}', $value, $template);
            }
        }
        
        // Send notification
        return self::sendNotification($type, $recipient, $template);
    }

    /**
     * Get system template
     */
    private static function getSystemTemplate($templateKey)
    {
        $template = \App\Models\SystemSetting::getValue('template_' . $templateKey);
        
        if ($template) {
            $templateData = json_decode($template, true);
            return $templateData['content'] ?? '';
        }
        
        return '';
    }
}

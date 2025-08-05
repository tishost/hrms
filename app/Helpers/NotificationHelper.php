<?php

namespace App\Helpers;

use App\Services\NotificationService;
use App\Helpers\SmsHelper;

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

        $variables = [
            'otp' => $otp,
        ];

        return self::sendTemplate('sms', $phone, 'otp_verification_sms', $variables);
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
        $variables = [
            'name' => $user->name,
            'email' => $user->email,
            'reset_url' => route('password.reset', ['token' => $resetToken, 'email' => $user->email])
        ];

        return self::sendTemplate('email', $user->email, 'password_reset_email', $variables);
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
}

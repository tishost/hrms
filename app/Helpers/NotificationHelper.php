<?php

namespace App\Helpers;

use App\Services\NotificationService;

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

        // Send email
        $results['email'] = self::sendTemplate('email', $user->email, 'welcome_email', $variables);

        // Send SMS if phone exists
        if ($user->phone) {
            $results['sms'] = self::sendTemplate('sms', $user->phone, 'welcome_sms', $variables);
        }

        return $results;
    }

    /**
     * Send OTP verification SMS
     */
    public static function sendOtpSms($phone, $otp)
    {
        $variables = [
            'otp' => $otp,
        ];

        return self::sendTemplate('sms', $phone, 'otp_verification_sms', $variables);
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

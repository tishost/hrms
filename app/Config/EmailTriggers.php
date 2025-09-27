<?php

namespace App\Config;

class EmailTriggers
{
    /**
     * Available email trigger events
     */
    public static function getAvailableTriggers()
    {
        return [
            'user_registration' => [
                'name' => 'User Registration',
                'event_class' => 'App\Events\UserRegistered',
                'description' => 'Triggered when a new user registers',
                'variables' => ['user_name', 'user_email', 'created_at', 'company_name'],
                'category' => 'system'
            ],
            'payment_completed' => [
                'name' => 'Payment Completed',
                'event_class' => 'App\Events\PaymentCompleted',
                'description' => 'Triggered when a payment is successfully completed',
                'variables' => ['user_name', 'amount', 'transaction_id', 'payment_date', 'company_name'],
                'category' => 'system'
            ],
            'invoice_generated' => [
                'name' => 'Invoice Generated',
                'event_class' => 'App\Events\InvoiceGenerated',
                'description' => 'Triggered when an invoice is generated',
                'variables' => ['user_name', 'invoice_number', 'amount', 'due_date', 'generated_date', 'company_name'],
                'category' => 'system'
            ],
            'system_notification' => [
                'name' => 'System Notification',
                'event_class' => 'App\Events\SystemNotification',
                'description' => 'Triggered for system-wide notifications',
                'variables' => ['user_name', 'notification_title', 'notification_message', 'notification_date', 'company_name'],
                'category' => 'system'
            ],
            'tenant_invitation' => [
                'name' => 'Tenant Invitation',
                'event_class' => 'App\Events\TenantInvitation',
                'description' => 'Triggered when inviting a new tenant',
                'variables' => ['tenant_name', 'invitation_link', 'property_name', 'company_name'],
                'category' => 'tenant'
            ],
            'owner_notification' => [
                'name' => 'Owner Notification',
                'event_class' => 'App\Events\OwnerNotification',
                'description' => 'Triggered for owner-specific notifications',
                'variables' => ['owner_name', 'notification_type', 'message', 'property_name', 'company_name'],
                'category' => 'owner'
            ],
            'rent_reminder' => [
                'name' => 'Rent Reminder',
                'event_class' => 'App\Events\RentReminder',
                'description' => 'Triggered for rent payment reminders',
                'variables' => ['tenant_name', 'property_name', 'rent_amount', 'due_date', 'days_remaining', 'company_name'],
                'category' => 'tenant'
            ],
            'maintenance_request' => [
                'name' => 'Maintenance Request',
                'event_class' => 'App\Events\MaintenanceRequest',
                'description' => 'Triggered when a maintenance request is submitted',
                'variables' => ['owner_name', 'tenant_name', 'property_name', 'request_id', 'priority', 'issue_description', 'submitted_date', 'company_name'],
                'category' => 'owner'
            ],
            'password_reset' => [
                'name' => 'Password Reset',
                'event_class' => 'App\Events\PasswordReset',
                'description' => 'Triggered when user requests password reset',
                'variables' => ['user_name', 'reset_link', 'otp', 'expiry_minutes', 'company_name'],
                'category' => 'system'
            ],
            'account_verification' => [
                'name' => 'Account Verification',
                'event_class' => 'App\Events\AccountVerification',
                'description' => 'Triggered for account verification emails',
                'variables' => ['user_name', 'verification_link', 'company_name'],
                'category' => 'system'
            ],
            'otp_sent' => [
                'name' => 'OTP Sent',
                'event_class' => 'App\Events\OtpSent',
                'description' => 'Triggered when OTP is sent to user',
                'variables' => ['user_name', 'otp', 'minutes', 'phone', 'company_name'],
                'category' => 'system'
            ],
            'otp_verification' => [
                'name' => 'OTP Verification',
                'event_class' => 'App\Events\OtpVerification',
                'description' => 'Triggered for OTP verification process',
                'variables' => ['user_name', 'otp', 'minutes', 'phone', 'verification_type', 'company_name'],
                'category' => 'system'
            ],
            'otp_registration' => [
                'name' => 'OTP Registration',
                'event_class' => 'App\Events\OtpRegistration',
                'description' => 'Triggered for OTP during user registration',
                'variables' => ['user_name', 'otp', 'minutes', 'phone', 'company_name'],
                'category' => 'system'
            ],
            'otp_password_reset' => [
                'name' => 'OTP Password Reset',
                'event_class' => 'App\Events\OtpPasswordReset',
                'description' => 'Triggered for OTP during password reset',
                'variables' => ['user_name', 'otp', 'minutes', 'phone', 'company_name'],
                'category' => 'system'
            ]
        ];
    }

    /**
     * Get trigger by key
     */
    public static function getTrigger($key)
    {
        $triggers = self::getAvailableTriggers();
        return $triggers[$key] ?? null;
    }

    /**
     * Get triggers by category
     */
    public static function getTriggersByCategory($category)
    {
        $triggers = self::getAvailableTriggers();
        return array_filter($triggers, function($trigger) use ($category) {
            return $trigger['category'] === $category;
        });
    }

    /**
     * Get all trigger categories
     */
    public static function getCategories()
    {
        $triggers = self::getAvailableTriggers();
        $categories = array_unique(array_column($triggers, 'category'));
        return array_combine($categories, array_map('ucfirst', $categories));
    }
}

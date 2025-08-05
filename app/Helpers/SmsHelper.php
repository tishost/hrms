<?php

namespace App\Helpers;

use App\Services\OwnerSmsService;

class SmsHelper
{
    private static $ownerSmsService = null;

    private static function getService()
    {
        if (self::$ownerSmsService === null) {
            self::$ownerSmsService = new OwnerSmsService();
        }
        return self::$ownerSmsService;
    }

    /**
     * Send SMS for Owner (deducts from owner's credits)
     */
    public static function sendOwnerSms($ownerId, $phoneNumber, $message, $templateName = null, $variables = [])
    {
        return self::getService()->sendOwnerSms($ownerId, $phoneNumber, $message, $templateName, $variables);
    }

    /**
     * Send SMS for Tenant (uses owner's SMS settings and credits)
     */
    public static function sendTenantSms($tenantId, $phoneNumber, $message, $templateName = null, $variables = [])
    {
        return self::getService()->sendTenantSms($tenantId, $phoneNumber, $message, $templateName, $variables);
    }

    /**
     * Send SMS using default system (when owner has no credits)
     */
    public static function sendSystemSms($phoneNumber, $message, $templateName = null, $variables = [])
    {
        return self::getService()->sendSystemSms($phoneNumber, $message, $templateName, $variables);
    }

    /**
     * Send SMS with automatic routing (Owner -> Tenant -> System)
     */
    public static function sendSmartSms($phoneNumber, $message, $ownerId = null, $tenantId = null, $templateName = null, $variables = [])
    {
        return self::getService()->sendSmartSms($phoneNumber, $message, $ownerId, $tenantId, $templateName, $variables);
    }

    /**
     * Send Owner Payment Confirmation SMS
     */
    public static function sendOwnerPaymentConfirmation($ownerId, $phoneNumber, $amount, $invoiceNumber, $paymentMethod)
    {
        return self::sendOwnerSms(
            $ownerId,
            $phoneNumber,
            null,
            'owner_payment_confirmation_sms',
            [
                'amount' => $amount,
                'invoice_number' => $invoiceNumber,
                'payment_method' => $paymentMethod
            ]
        );
    }

    /**
     * Send Owner Invoice Notification SMS
     */
    public static function sendOwnerInvoiceNotification($ownerId, $phoneNumber, $amount, $invoiceNumber, $dueDate)
    {
        return self::sendOwnerSms(
            $ownerId,
            $phoneNumber,
            null,
            'owner_invoice_notification_sms',
            [
                'amount' => $amount,
                'invoice_number' => $invoiceNumber,
                'due_date' => $dueDate
            ]
        );
    }

    /**
     * Send Owner Subscription Reminder SMS
     */
    public static function sendOwnerSubscriptionReminder($ownerId, $phoneNumber, $expiryDate)
    {
        return self::sendOwnerSms(
            $ownerId,
            $phoneNumber,
            null,
            'owner_subscription_reminder_sms',
            [
                'expiry_date' => $expiryDate
            ]
        );
    }

    /**
     * Send Owner Welcome SMS
     */
    public static function sendOwnerWelcome($ownerId, $phoneNumber, $ownerName)
    {
        return self::sendOwnerSms(
            $ownerId,
            $phoneNumber,
            null,
            'owner_welcome_sms',
            [
                'name' => $ownerName
            ]
        );
    }

    /**
     * Send Tenant Payment Confirmation SMS
     */
    public static function sendTenantPaymentConfirmation($tenantId, $phoneNumber, $amount, $propertyName)
    {
        return self::sendTenantSms(
            $tenantId,
            $phoneNumber,
            null,
            'tenant_payment_confirmation_sms',
            [
                'amount' => $amount,
                'property_name' => $propertyName
            ]
        );
    }

    /**
     * Send Tenant Invoice Notification SMS
     */
    public static function sendTenantInvoiceNotification($tenantId, $phoneNumber, $amount, $dueDate, $propertyName)
    {
        return self::sendTenantSms(
            $tenantId,
            $phoneNumber,
            null,
            'tenant_invoice_notification_sms',
            [
                'amount' => $amount,
                'due_date' => $dueDate,
                'property_name' => $propertyName
            ]
        );
    }

    /**
     * Send Tenant Rent Reminder SMS
     */
    public static function sendTenantRentReminder($tenantId, $phoneNumber, $amount, $dueDate, $propertyName)
    {
        return self::sendTenantSms(
            $tenantId,
            $phoneNumber,
            null,
            'tenant_subscription_reminder_sms',
            [
                'amount' => $amount,
                'due_date' => $dueDate,
                'property_name' => $propertyName
            ]
        );
    }

    /**
     * Send Tenant Welcome SMS
     */
    public static function sendTenantWelcome($tenantId, $phoneNumber, $tenantName, $propertyName)
    {
        return self::sendTenantSms(
            $tenantId,
            $phoneNumber,
            null,
            'tenant_welcome_sms',
            [
                'tenant_name' => $tenantName,
                'property_name' => $propertyName
            ]
        );
    }
} 
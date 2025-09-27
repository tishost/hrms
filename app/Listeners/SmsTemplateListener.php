<?php

namespace App\Listeners;

use App\Models\SmsTemplate;
use App\Models\User;
use App\Events\UserRegistered;
use App\Events\PaymentCompleted;
use App\Events\InvoiceGenerated;
use App\Events\SystemNotification;
use App\Events\TenantInvitation;
use App\Events\OwnerNotification;
use App\Events\RentReminder;
use App\Events\MaintenanceRequest;
use App\Events\PasswordReset;
use App\Events\AccountVerification;
use App\Events\OtpSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SmsNotification;

class SmsTemplateListener
{
    /**
     * Handle user registration events
     */
    public function handleUserRegistered(UserRegistered $event)
    {
        $this->sendTemplateSms('user_registration', $event->user, [
            'user_name' => $event->user->name,
            'user_email' => $event->user->email,
            'created_at' => $event->user->created_at->format('Y-m-d H:i:s'),
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle payment completed events
     */
    public function handlePaymentCompleted(PaymentCompleted $event)
    {
        $this->sendTemplateSms('payment_completed', $event->user, [
            'user_name' => $event->user->name,
            'amount' => $event->amount,
            'transaction_id' => $event->transactionId,
            'payment_date' => now()->format('Y-m-d H:i:s'),
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle invoice generated events
     */
    public function handleInvoiceGenerated(InvoiceGenerated $event)
    {
        $this->sendTemplateSms('invoice_generated', $event->user, [
            'user_name' => $event->user->name,
            'invoice_number' => $event->invoiceNumber,
            'amount' => $event->amount,
            'due_date' => $event->dueDate,
            'generated_date' => now()->format('Y-m-d H:i:s'),
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle system notification events
     */
    public function handleSystemNotification(SystemNotification $event)
    {
        $this->sendTemplateSms('system_notification', $event->user, [
            'user_name' => $event->user->name,
            'notification_title' => $event->title,
            'notification_message' => $event->message,
            'notification_date' => now()->format('Y-m-d H:i:s'),
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle tenant invitation events
     */
    public function handleTenantInvitation(TenantInvitation $event)
    {
        $this->sendTemplateSms('tenant_invitation', $event->user, [
            'tenant_name' => $event->user->name,
            'invitation_link' => $event->invitationLink,
            'property_name' => $event->propertyName,
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle owner notification events
     */
    public function handleOwnerNotification(OwnerNotification $event)
    {
        $this->sendTemplateSms('owner_notification', $event->user, [
            'owner_name' => $event->user->name,
            'notification_type' => $event->type,
            'message' => $event->message,
            'property_name' => $event->propertyName,
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle rent reminder events
     */
    public function handleRentReminder(RentReminder $event)
    {
        $this->sendTemplateSms('rent_reminder', $event->user, [
            'tenant_name' => $event->user->name,
            'property_name' => $event->propertyName,
            'rent_amount' => $event->rentAmount,
            'due_date' => $event->dueDate,
            'days_remaining' => $event->daysRemaining,
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle maintenance request events
     */
    public function handleMaintenanceRequest(MaintenanceRequest $event)
    {
        $this->sendTemplateSms('maintenance_request', $event->user, [
            'owner_name' => $event->user->name,
            'tenant_name' => $event->tenantName,
            'property_name' => $event->propertyName,
            'request_id' => $event->requestId,
            'priority' => $event->priority,
            'issue_description' => $event->issueDescription,
            'submitted_date' => now()->format('Y-m-d H:i:s'),
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle password reset events
     */
    public function handlePasswordReset(PasswordReset $event)
    {
        $this->sendTemplateSms('password_reset', $event->user, [
            'user_name' => $event->user->name,
            'reset_link' => $event->resetLink,
            'otp' => $event->otp,
            'expiry_minutes' => $event->expiryMinutes,
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle account verification events
     */
    public function handleAccountVerification(AccountVerification $event)
    {
        $this->sendTemplateSms('account_verification', $event->user, [
            'user_name' => $event->user->name,
            'verification_link' => $event->verificationLink,
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Handle OTP sent events
     */
    public function handleOtpSent(OtpSent $event)
    {
        // Create a temporary user object if needed for template system
        $user = $event->user;
        if (!$user) {
            $user = new \stdClass();
            $user->id = 0;
            $user->name = 'User';
            $user->phone = $event->phone;
        }

        $this->sendTemplateSms('otp_sent', $user, [
            'user_name' => $user->name ?? 'User',
            'otp' => $event->otp,
            'minutes' => $event->minutes,
            'phone' => $event->phone,
            'verification_type' => $event->type,
            'company_name' => \App\Helpers\SystemHelper::getCompanyName()
        ]);
    }

    /**
     * Send template-based SMS
     */
    private function sendTemplateSms($templateName, $user, $variables = [])
    {
        try {
            // Find the SMS template by trigger event first, then by name as fallback
            $template = SmsTemplate::where('trigger_event', $templateName)
                ->where('is_active', true)
                ->orderBy('priority', 'asc')
                ->first();

            // Fallback to name-based lookup if no trigger-based template found
            if (!$template) {
                $template = SmsTemplate::where('name', $templateName)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$template) {
                Log::warning("SMS template for trigger '{$templateName}' not found or inactive");
                return;
            }

            // Check if user has phone number
            if (!$user->phone) {
                Log::warning("User {$user->id} has no phone number");
                return;
            }

            // Replace variables in content
            $content = $template->content;
            foreach ($variables as $key => $value) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }

            // Send the SMS
            Notification::send($user, new SmsNotification($content));

            Log::info("SMS template '{$template->name}' (trigger: {$templateName}) sent to {$user->phone}");

        } catch (\Exception $e) {
            Log::error("Failed to send SMS template '{$templateName}': " . $e->getMessage());
        }
    }
}

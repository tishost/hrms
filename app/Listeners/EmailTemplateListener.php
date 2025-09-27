<?php

namespace App\Listeners;

use App\Models\EmailTemplate;
use App\Mail\TemplateEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailTemplateListener
{
    /**
     * Handle user registration event
     */
    public function handleUserRegistered($event)
    {
        $this->sendTemplateEmail('user_registration', $event->user, [
            'user_name' => $event->user->name,
            'user_email' => $event->user->email,
            'created_at' => $event->user->created_at->format('Y-m-d H:i:s'),
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Handle password reset event
     */
    public function handlePasswordReset($event)
    {
        $this->sendTemplateEmail('password_reset', $event->user, [
            'user_name' => $event->user->name,
            'reset_link' => $event->resetLink,
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Handle payment completion event
     */
    public function handlePaymentCompleted($event)
    {
        $this->sendTemplateEmail('payment_confirmation', $event->user, [
            'user_name' => $event->user->name,
            'amount' => $event->payment->amount,
            'transaction_id' => $event->payment->transaction_id,
            'payment_date' => $event->payment->created_at->format('Y-m-d H:i:s'),
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Handle invoice generation event
     */
    public function handleInvoiceGenerated($event)
    {
        $this->sendTemplateEmail('invoice_generated', $event->user, [
            'user_name' => $event->user->name,
            'invoice_number' => $event->invoice->invoice_number,
            'amount' => $event->invoice->total_amount,
            'due_date' => $event->invoice->due_date->format('Y-m-d'),
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Handle system notification event
     */
    public function handleSystemNotification($event)
    {
        $this->sendTemplateEmail('system_notification', $event->user, [
            'user_name' => $event->user->name,
            'notification_title' => $event->notification->title,
            'notification_message' => $event->notification->message,
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Handle tenant invitation event
     */
    public function handleTenantInvitation($event)
    {
        $this->sendTemplateEmail('tenant_invitation', $event->tenant, [
            'tenant_name' => $event->tenant->name,
            'invitation_link' => $event->invitationLink,
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Handle owner notification event
     */
    public function handleOwnerNotification($event)
    {
        $this->sendTemplateEmail('owner_notification', $event->owner, [
            'owner_name' => $event->owner->name,
            'notification_type' => $event->notificationType,
            'message' => $event->message,
            'company_name' => config('app.name'),
        ]);
    }

    /**
     * Generic method to send template emails
     */
    private function sendTemplateEmail($templateName, $user, $variables = [])
    {
        try {
            // Find the email template by trigger event first, then by name as fallback
            $template = EmailTemplate::where('trigger_event', $templateName)
                ->where('is_active', true)
                ->orderBy('priority', 'asc')
                ->first();

            // Fallback to name-based lookup if no trigger-based template found
            if (!$template) {
                $template = EmailTemplate::where('name', $templateName)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$template) {
                Log::warning("Email template for trigger '{$templateName}' not found or inactive");
                return;
            }

            // Check if user has email
            if (!$user->email) {
                Log::warning("User {$user->id} has no email address");
                return;
            }

            // Send the email
            Mail::to($user->email)->send(new TemplateEmail($template, $variables));

            Log::info("Email template '{$template->name}' (trigger: {$templateName}) sent to {$user->email}");

        } catch (\Exception $e) {
            Log::error("Failed to send email template '{$templateName}': " . $e->getMessage());
        }
    }
}

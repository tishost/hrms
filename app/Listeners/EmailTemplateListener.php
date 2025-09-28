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
     * Handle tenant registration event
     */
    public function handleTenantRegistered($event)
    {
        // Get property and unit information
        $property = $event->property ?? $event->tenant->unit->property ?? null;
        $unit = $event->unit ?? $event->tenant->unit ?? null;
        
        // Get rent information
        $monthlyRent = $unit->rent ?? $event->tenant->rent ?? 0;
        $securityDeposit = $event->tenant->security_deposit ?? 0;
        
        // Get company information
        $companyName = \App\Helpers\SystemHelper::getCompanyName();
        
        // Use user's email if tenant doesn't have a valid email
        $emailToUse = $event->user->email;
        if (!$emailToUse || $emailToUse === 'N/A' || !filter_var($emailToUse, FILTER_VALIDATE_EMAIL)) {
            $emailToUse = $event->tenant->email;
        }
        
        // Final validation - if still no valid email, skip sending
        if (!$emailToUse || $emailToUse === 'N/A' || !filter_var($emailToUse, FILTER_VALIDATE_EMAIL)) {
            Log::warning("Skipping email notification for tenant {$event->tenant->id} - no valid email address found");
            return;
        }
        
        $this->sendTemplateEmail('tenant_registration_welcome', $event->user, [
            'tenant_name' => trim(($event->tenant->first_name ?? '') . ' ' . ($event->tenant->last_name ?? '')),
            'tenant_mobile' => $event->tenant->mobile ?? $event->user->phone,
            'tenant_email' => $emailToUse,
            'property_name' => $property->name ?? 'Property',
            'unit_name' => $unit->name ?? 'Unit',
            'registration_date' => $event->tenant->created_at->format('F j, Y'),
            'monthly_rent' => number_format($monthlyRent),
            'security_deposit' => number_format($securityDeposit),
            'rent_due_date' => $unit->rent_due_date ?? '1st',
            'payment_method' => 'Mobile Banking / Bank Transfer',
            'utilities_included' => $unit->utilities_included ?? 'Utilities not included',
            'notice_period' => '1 month',
            'app_download_link' => config('app.url') . '/download',
            'property_contact' => $property->mobile ?? $property->phone ?? 'Contact property management',
            'emergency_contact' => $property->emergency_contact ?? 'Emergency contact not set',
            'support_email' => config('mail.from.address'),
            'office_hours' => '9:00 AM - 6:00 PM (Sunday to Thursday)',
            'company_name' => $companyName,
            'current_year' => date('Y'),
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
     * Handle subscription order event
     */
    public function handleSubscriptionOrder($event)
    {
        $this->sendTemplateEmail('subscription_order', $event->owner->user, [
            'owner_name' => $event->owner->name,
            'owner_email' => $event->owner->email,
            'plan_name' => $event->plan->name,
            'plan_price' => $event->plan->price,
            'invoice_number' => $event->billing->invoice_number,
            'order_date' => $event->billing->created_at->format('Y-m-d H:i:s'),
            'due_date' => $event->billing->due_date->format('Y-m-d'),
            'payment_method' => 'Online Payment',
            'billing_cycle' => 'Annual',
            'features_included' => $this->getPlanFeatures($event->plan),
            'company_name' => config('app.name'),
            'support_email' => config('mail.from.address'),
            'invoice_download_link' => route('owner.subscription.invoice.download', $event->billing->id)
        ]);
    }

    /**
     * Handle subscription activation event
     */
    public function handleSubscriptionActivated($event)
    {
        $this->sendTemplateEmail('subscription_activation', $event->owner->user, [
            'owner_name' => $event->owner->name,
            'owner_email' => $event->owner->email,
            'plan_name' => $event->plan->name,
            'subscription_start_date' => $event->subscription->start_date->format('Y-m-d'),
            'subscription_end_date' => $event->subscription->end_date->format('Y-m-d'),
            'company_name' => config('app.name'),
            'support_email' => config('mail.from.address')
        ]);
    }

    /**
     * Get plan features as string
     */
    private function getPlanFeatures($plan)
    {
        $features = [];
        
        if ($plan->properties_limit > 0) {
            $features[] = "Up to {$plan->properties_limit} properties";
        }
        
        if ($plan->units_limit > 0) {
            $features[] = "Up to {$plan->units_limit} units";
        }
        
        if ($plan->tenants_limit > 0) {
            $features[] = "Up to {$plan->tenants_limit} tenants";
        }
        
        if ($plan->sms_notification) {
            $features[] = "SMS notifications included";
        }
        
        if ($plan->email_notification) {
            $features[] = "Email notifications included";
        }
        
        if ($plan->maintenance_tracking) {
            $features[] = "Maintenance tracking";
        }
        
        if ($plan->financial_reporting) {
            $features[] = "Financial reporting";
        }
        
        return implode(', ', $features);
    }

    /**
     * Generic method to send template emails
     */
    private function sendTemplateEmail($templateName, $user, $variables = [])
    {
        try {
            // Find the email template by trigger event first, then by key, then by name as fallback
            $template = EmailTemplate::where('trigger_event', $templateName)
                ->where('is_active', true)
                ->orderBy('priority', 'asc')
                ->first();

            // Fallback to key-based lookup if no trigger-based template found
            if (!$template) {
                $template = EmailTemplate::where('key', $templateName)
                    ->where('is_active', true)
                    ->first();
            }

            // Fallback to name-based lookup if no key-based template found
            if (!$template) {
                $template = EmailTemplate::where('name', $templateName)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$template) {
                Log::warning("Email template for trigger '{$templateName}' not found or inactive");
                return;
            }

            // Check if user has valid email
            if (!$user->email || $user->email === 'N/A' || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Log::warning("User {$user->id} has no valid email address: " . ($user->email ?? 'null'));
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

<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Custom Events for Email and SMS Templates
        \App\Events\UserRegistered::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleUserRegistered',
            \App\Listeners\SmsTemplateListener::class . '@handleUserRegistered',
        ],

        \App\Events\PaymentCompleted::class => [
            \App\Listeners\EmailTemplateListener::class . '@handlePaymentCompleted',
            \App\Listeners\SmsTemplateListener::class . '@handlePaymentCompleted',
        ],

        \App\Events\InvoiceGenerated::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleInvoiceGenerated',
            \App\Listeners\SmsTemplateListener::class . '@handleInvoiceGenerated',
        ],

        \App\Events\SystemNotification::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleSystemNotification',
            \App\Listeners\SmsTemplateListener::class . '@handleSystemNotification',
        ],

        \App\Events\TenantInvitation::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleTenantInvitation',
            \App\Listeners\SmsTemplateListener::class . '@handleTenantInvitation',
        ],

        \App\Events\OwnerNotification::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleOwnerNotification',
            \App\Listeners\SmsTemplateListener::class . '@handleOwnerNotification',
        ],

        \App\Events\RentReminder::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleRentReminder',
            \App\Listeners\SmsTemplateListener::class . '@handleRentReminder',
        ],

        \App\Events\MaintenanceRequest::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleMaintenanceRequest',
            \App\Listeners\SmsTemplateListener::class . '@handleMaintenanceRequest',
        ],

        \App\Events\PasswordReset::class => [
            \App\Listeners\EmailTemplateListener::class . '@handlePasswordReset',
            \App\Listeners\SmsTemplateListener::class . '@handlePasswordReset',
        ],

        \App\Events\AccountVerification::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleAccountVerification',
            \App\Listeners\SmsTemplateListener::class . '@handleAccountVerification',
        ],

        \App\Events\OtpSent::class => [
            \App\Listeners\SmsTemplateListener::class . '@handleOtpSent',
        ],

        \App\Events\TenantRegistered::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleTenantRegistered',
            \App\Listeners\SmsTemplateListener::class . '@handleTenantRegistered',
        ],

        \App\Events\SubscriptionOrder::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleSubscriptionOrder',
        ],

        \App\Events\SubscriptionPaymentConfirmed::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleSubscriptionPaymentConfirmed',
        ],

        \App\Events\SubscriptionActivated::class => [
            \App\Listeners\EmailTemplateListener::class . '@handleSubscriptionActivated',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

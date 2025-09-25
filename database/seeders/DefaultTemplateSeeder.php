<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailTemplate;
use App\Models\SmsTemplate;

class DefaultTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding default email and SMS templates...');

        // Clear existing templates (only if tables exist)
        if (Schema::hasTable('email_templates')) {
            EmailTemplate::truncate();
        }
        if (Schema::hasTable('sms_templates')) {
            SmsTemplate::truncate();
        }

        // Email Templates
        $emailTemplates = [
            [
                'key' => 'welcome_email',
                'name' => 'Welcome Email',
                'subject' => 'Welcome to HRMS',
                'content' => 'Dear {user_name}, Welcome to HRMS! Your account has been created successfully. Thank you for joining us. Best regards, {company_name}',
                'category' => 'system',
                'is_active' => 1,
                'priority' => 1,
                'description' => 'Welcome email for new users',
                'tags' => 'welcome,user,registration'
            ],
            [
                'key' => 'password_reset_email',
                'name' => 'Password Reset Email',
                'subject' => 'Password Reset Request',
                'content' => 'Dear {user_name}, You have requested to reset your password. Your OTP: {otp} This OTP is valid for 10 minutes. If you did not request this, please ignore this email. Thank you, {company_name}',
                'category' => 'system',
                'is_active' => 1,
                'priority' => 1,
                'description' => 'Password reset email with OTP',
                'tags' => 'password,reset,otp,security'
            ],
            [
                'key' => 'account_verification_email',
                'name' => 'Account Verification Email',
                'subject' => 'Verify Your Account',
                'content' => 'Dear {user_name}, Please verify your account by clicking the link: {verification_url} Thank you, {company_name}',
                'category' => 'system',
                'is_active' => 1,
                'priority' => 1,
                'description' => 'Account verification email',
                'tags' => 'verification,account,email'
            ],
            [
                'key' => 'payment_confirmation_email',
                'name' => 'Payment Confirmation Email',
                'subject' => 'Payment Confirmation',
                'content' => 'Dear {user_name}, Your payment of ৳{amount} has been received successfully. Invoice: {invoice_number} Payment Method: {payment_method} Thank you, {company_name}',
                'category' => 'owner',
                'is_active' => 1,
                'priority' => 2,
                'description' => 'Payment confirmation email',
                'tags' => 'payment,confirmation,invoice'
            ],
            [
                'key' => 'subscription_activation_email',
                'name' => 'Subscription Activation Email',
                'subject' => 'Subscription Activated',
                'content' => 'Dear {user_name}, Your subscription has been activated successfully. Plan: {plan_name} Duration: {duration} Thank you for choosing HRMS!',
                'category' => 'owner',
                'is_active' => 1,
                'priority' => 2,
                'description' => 'Subscription activation email',
                'tags' => 'subscription,activation,plan'
            ]
        ];

        foreach ($emailTemplates as $template) {
            EmailTemplate::create($template);
            $this->command->line("✅ Created email template: {$template['name']}");
        }

        // SMS Templates
        $smsTemplates = [
            [
                'key' => 'welcome_sms',
                'name' => 'Welcome SMS',
                'content' => 'Welcome {user_name}! Your HRMS account is ready. Thank you for joining us. - {company_name}',
                'category' => 'system',
                'is_active' => 1,
                'priority' => 1,
                'character_limit' => 160,
                'unicode_support' => 1,
                'description' => 'Welcome SMS for new users',
                'tags' => 'welcome,user,registration'
            ],
            [
                'key' => 'system_otp_sms',
                'name' => 'System OTP SMS',
                'content' => 'Your OTP: {otp}. Valid for 10 minutes. {company_name}',
                'category' => 'system',
                'is_active' => 1,
                'priority' => 1,
                'character_limit' => 160,
                'unicode_support' => 1,
                'description' => 'OTP SMS for system verification',
                'tags' => 'otp,verification,security'
            ],
            [
                'key' => 'owner_welcome_sms',
                'name' => 'Owner Welcome SMS',
                'content' => 'Welcome {owner_name}! Your HRMS owner account is activated. Start managing your properties now! - {company_name}',
                'category' => 'owner',
                'is_active' => 1,
                'priority' => 2,
                'character_limit' => 160,
                'unicode_support' => 1,
                'description' => 'Welcome SMS for new owners',
                'tags' => 'welcome,owner,registration'
            ],
            [
                'key' => 'payment_confirmation_sms',
                'name' => 'Payment Confirmation SMS',
                'content' => 'Payment received: ৳{amount}. Invoice: {invoice_number}. Thank you! - {company_name}',
                'category' => 'owner',
                'is_active' => 1,
                'priority' => 2,
                'character_limit' => 160,
                'unicode_support' => 1,
                'description' => 'Payment confirmation SMS',
                'tags' => 'payment,confirmation,invoice'
            ],
            [
                'key' => 'subscription_activation_sms',
                'name' => 'Subscription Activation SMS',
                'content' => 'Subscription activated! Plan: {plan_name}. Start using HRMS features now! - {company_name}',
                'category' => 'owner',
                'is_active' => 1,
                'priority' => 2,
                'character_limit' => 160,
                'unicode_support' => 1,
                'description' => 'Subscription activation SMS',
                'tags' => 'subscription,activation,plan'
            ]
        ];

        foreach ($smsTemplates as $template) {
            SmsTemplate::create($template);
            $this->command->line("✅ Created SMS template: {$template['name']}");
        }

        $this->command->info('🎉 Default templates seeded successfully!');
        $this->command->line('Email templates: ' . EmailTemplate::count());
        $this->command->line('SMS templates: ' . SmsTemplate::count());
    }
}

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
                'subject' => 'Welcome to BariManager',
                'content' => '<h1 style="margin:0 0 12px 0; font-size:26px; line-height:1.2; color:#111;">Welcome, {user_name}!</h1>
<p style="margin:0 0 18px 0; font-size:15px; color:#555;">
    Your {company_name} account has been created successfully. We\'re excited to have you on board!
</p>

<!-- Account Details -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px; border-radius:8px; background:#fbfdff; border:1px solid #eef4ff;">
    <tr>
        <td style="padding:16px;">
            <h3 style="margin:0 0 10px 0; font-size:16px; color:#111;">Your Account Details</h3>
            <p style="margin:0 0 6px 0; font-size:14px; color:#333;"><strong>Email:</strong> {user_email}</p>
            <p style="margin:0 0 6px 0; font-size:14px; color:#333;"><strong>Registration Date:</strong> {created_at}</p>
            <p style="margin:0 0 0 0; font-size:14px; color:#333;"><strong>Status:</strong> <span style="color:#28a745;">✅ Active</span></p>
        </td>
    </tr>
</table>

<!-- CTA Button -->
<table cellpadding="0" cellspacing="0" role="presentation" style="margin-top:18px;">
    <tr>
        <td align="left">
            <a href="{site_url}/dashboard" class="btn">Go to Dashboard</a>
        </td>
    </tr>
</table>

<p style="margin:18px 0 0 0; font-size:13px; color:#777;">
    Best regards,<br>
    The {company_name} Team
</p>',
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
                'content' => '<h1 style="margin:0 0 12px 0; font-size:26px; line-height:1.2; color:#111;">Password Reset Request</h1>
<p style="margin:0 0 18px 0; font-size:15px; color:#555;">
    Dear {user_name}, you have requested to reset your password for your {company_name} account.
</p>

<!-- OTP Code -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px; border-radius:8px; background:#fff3cd; border:1px solid #ffeaa7;">
    <tr>
        <td style="padding:20px; text-align:center;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#856404;">Your OTP Code</h3>
            <div style="background:#fff; border:2px dashed #ffc107; padding:20px; margin:15px 0; border-radius:8px;">
                <h1 style="color:#dc3545; margin:0; font-size:36px; font-weight:bold; letter-spacing:5px;">{otp}</h1>
            </div>
            <p style="margin:0; color:#856404; font-weight:600;">⏰ This OTP is valid for 10 minutes</p>
        </td>
    </tr>
</table>

<p style="margin:18px 0; font-size:14px; color:#555;">
    Please use this OTP to reset your password. If you did not request this password reset, please ignore this email.
</p>

<!-- Security Tips -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px; border-radius:8px; background:#d1ecf1; border:1px solid #bee5eb;">
    <tr>
        <td style="padding:16px;">
            <h4 style="margin:0 0 10px 0; font-size:14px; color:#0c5460;">🛡️ Security Tips:</h4>
            <ul style="margin:0; padding-left:20px; color:#0c5460;">
                <li>Never share your OTP with anyone</li>
                <li>Use a strong password with letters, numbers, and symbols</li>
                <li>Change your password regularly</li>
            </ul>
        </td>
    </tr>
</table>

<p style="margin:18px 0 0 0; font-size:13px; color:#777;">
    Thank you,<br>
    The {company_name} Team
</p>',
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
                'content' => '<h1 style="margin:0 0 12px 0; font-size:26px; line-height:1.2; color:#111;">Account Verification Required</h1>
<p style="margin:0 0 18px 0; font-size:15px; color:#555;">
    Dear {user_name}, thank you for registering with {company_name}! To complete your account setup and unlock all features, please verify your email address.
</p>

<!-- Verification Button -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px; border-radius:8px; background:#d1ecf1; border:1px solid #bee5eb;">
    <tr>
        <td style="padding:20px; text-align:center;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#0c5460;">Email Verification Required</h3>
            <p style="margin:0 0 20px 0; font-size:14px; color:#0c5460;">Click the button below to verify your account and get started:</p>
            <a href="{verification_url}" class="btn" style="display:inline-block; padding:15px 30px; background:#1e88e5; color:white; text-decoration:none; border-radius:6px; font-size:16px; font-weight:600;">🚀 Verify My Account</a>
        </td>
    </tr>
</table>

<!-- Alternative Link -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px; border-radius:8px; background:#f8f9fa; border:1px solid #e9ecef;">
    <tr>
        <td style="padding:16px;">
            <p style="margin:0 0 10px 0; font-size:14px; color:#6c757d;"><strong>Alternative:</strong> If the button doesn\'t work, you can copy and paste this link into your browser:</p>
            <p style="margin:0; word-break:break-all;"><a href="{verification_url}" style="color:#1e88e5; text-decoration:none;">{verification_url}</a></p>
        </td>
    </tr>
</table>

<!-- Important Notice -->
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:12px; border-radius:8px; background:#fff3cd; border:1px solid #ffeaa7;">
    <tr>
        <td style="padding:16px;">
            <p style="margin:0; color:#856404; font-size:14px;"><strong>⏰ Important:</strong> This verification link will expire in 24 hours for security reasons.</p>
        </td>
    </tr>
</table>

<p style="margin:18px 0 0 0; font-size:13px; color:#777;">
    Thank you,<br>
    The {company_name} Team
</p>',
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
                'content' => '<h2>Payment Confirmation</h2>
<p>Dear <strong>{user_name}</strong>,</p>
<p>Your payment has been received successfully!</p>
<div class="alert alert-success">
    <h4>Payment Details:</h4>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Amount:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">৳{amount}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Invoice Number:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{invoice_number}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Payment Method:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{payment_method}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Transaction Date:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{payment_date}</td>
        </tr>
    </table>
</div>
<p>Thank you for your payment. Your account has been updated accordingly.</p>
<p>Best regards,<br><strong>{company_name}</strong></p>',
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
                'content' => 'Dear {user_name}, Your subscription has been activated successfully. Plan: {plan_name} Duration: {duration} Thank you for choosing BariManager!',
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
                'content' => 'Welcome {user_name}! Your BariManager account is ready. Thank you for joining us. - {company_name}',
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
                'content' => 'Your OTP: {otp}. Valid for {minutes} minutes. {company_name}',
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
                'content' => 'Welcome {owner_name}! Your BariManager owner account is activated. Start managing your properties now! - {company_name}',
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
                'content' => 'Subscription activated! Plan: {plan_name}. Start using BariManager features now! - {company_name}',
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


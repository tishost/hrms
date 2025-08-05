<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

echo "Adding all required templates...\n";

// Email Templates
$emailTemplates = [
    'welcome_email' => [
        'subject' => 'Welcome to HRMS - Your Account is Ready!',
        'content' => 'Dear {{name}},

Welcome to HRMS! Your account has been successfully created.

Your login details:
Email: {{email}}

We are excited to have you on board. If you have any questions, please don\'t hesitate to contact our support team.

Best regards,
The HRMS Team'
    ],
    
    'account_setup_guide_email' => [
        'subject' => 'Getting Started with HRMS - Setup Guide',
        'content' => 'Dear {{name}},

Thank you for joining HRMS! Here\'s your quick setup guide:

1. Complete your profile
2. Add your properties
3. Invite tenants
4. Set up billing

For detailed instructions, visit our help center.

Best regards,
The HRMS Team'
    ],
    
    'features_overview_email' => [
        'subject' => 'Discover HRMS Features - Everything You Need',
        'content' => 'Dear {{name}},

Here are the key features available in your HRMS account:

• Property Management
• Tenant Management
• Rent Collection
• Financial Reports
• SMS Notifications
• Document Management

Explore these features to maximize your property management efficiency.

Best regards,
The HRMS Team'
    ],
    
    'subscription_info_email' => [
        'subject' => 'Your HRMS Subscription Details',
        'content' => 'Dear {{name}},

Your subscription details:

Plan: {{plan_name}}
Price: ${{plan_price}}
Expiry Date: {{expiry_date}}
SMS Credits: {{sms_credits}}

Thank you for choosing HRMS!

Best regards,
The HRMS Team'
    ],
    
    'payment_confirmation_email' => [
        'subject' => 'Payment Confirmation - HRMS',
        'content' => 'Dear {{name}},

Thank you for your payment!

Payment Details:
Amount: ${{amount}}
Invoice Number: {{invoice_number}}
Payment Method: {{payment_method}}

Your payment has been processed successfully.

Best regards,
The HRMS Team'
    ],
    
    'invoice_notification_email' => [
        'subject' => 'New Invoice Generated - HRMS',
        'content' => 'Dear {{name}},

A new invoice has been generated for your account.

Invoice Details:
Amount: ${{amount}}
Invoice Number: {{invoice_number}}
Due Date: {{due_date}}

Please make the payment before the due date.

Best regards,
The HRMS Team'
    ],
    
    'subscription_reminder_email' => [
        'subject' => 'Subscription Reminder - HRMS',
        'content' => 'Dear {{name}},

Your subscription reminder:

Plan: {{plan_name}}
Expiry Date: {{expiry_date}}

Please renew your subscription to continue enjoying our services.

Best regards,
The HRMS Team'
    ],
    
    'subscription_activation_email' => [
        'subject' => 'Subscription Activated - HRMS',
        'content' => 'Dear {{name}},

Your subscription has been activated successfully!

Plan: {{plan_name}}
Expiry Date: {{expiry_date}}

Thank you for choosing HRMS!

Best regards,
The HRMS Team'
    ],
    
    'account_verification_email' => [
        'subject' => 'Verify Your Account - HRMS',
        'content' => 'Dear {{name}},

Please verify your email address by clicking the link below:

{{verification_url}}

This link will expire in 24 hours.

Best regards,
The HRMS Team'
    ],
    
    'password_reset_email' => [
        'subject' => 'Password Reset Request - HRMS',
        'content' => 'Dear {{name}},

You have requested to reset your password. Click the link below to reset it:

{{reset_url}}

This link will expire in 60 minutes.

If you didn\'t request this, please ignore this email.

Best regards,
The HRMS Team'
    ],
    
    'security_alert_email' => [
        'subject' => 'Security Alert - HRMS',
        'content' => 'Dear {{name}},

We detected unusual activity on your account:

Activity: {{activity}}
Time: {{timestamp}}
IP Address: {{ip_address}}

If this wasn\'t you, please contact support immediately.

Best regards,
The HRMS Team'
    ],
    
    'subscription_expiry_reminder_email' => [
        'subject' => 'Subscription Expiring Soon - HRMS',
        'content' => 'Dear {{name}},

Your subscription will expire in {{days_left}} days.

Plan: {{plan_name}}
Expiry Date: {{expiry_date}}

Please renew your subscription to avoid service interruption.

Best regards,
The HRMS Team'
    ],
    
    'payment_success_email' => [
        'subject' => 'Payment Successful - HRMS',
        'content' => 'Dear {{name}},

Your payment has been processed successfully!

Payment Details:
Amount: ${{amount}}
Transaction ID: {{transaction_id}}
Payment Method: {{payment_method}}
Date: {{payment_date}}

Thank you for your payment!

Best regards,
The HRMS Team'
    ],
    
    'invoice_reminder_email' => [
        'subject' => 'Invoice Reminder - HRMS',
        'content' => 'Dear {{name}},

This is a reminder for your pending invoice:

Invoice Number: {{invoice_number}}
Amount: ${{amount}}
Due Date: {{due_date}}

Payment Link: {{payment_url}}

Please make the payment before the due date.

Best regards,
The HRMS Team'
    ]
];

// SMS Templates
$smsTemplates = [
    'welcome_sms' => [
        'subject' => 'Welcome to HRMS',
        'content' => 'Welcome {{name}}! Your HRMS account is ready. Login at our website to get started.'
    ],
    
    'payment_confirmation_sms' => [
        'subject' => 'Payment Confirmation',
        'content' => 'Payment of ${{amount}} received. Invoice: {{invoice_number}}. Thank you!'
    ],
    
    'due_date_reminder_sms' => [
        'subject' => 'Due Date Reminder',
        'content' => 'Invoice {{invoice_number}} for ${{amount}} is due on {{due_date}}. Please pay on time.'
    ],
    
    'subscription_activation_sms' => [
        'subject' => 'Subscription Activated',
        'content' => 'Your {{plan_name}} subscription is now active. Expires: {{expiry_date}}. Thank you!'
    ],
    
    'otp_verification_sms' => [
        'subject' => 'OTP Verification',
        'content' => 'Your OTP is {{otp}}. Use this code to verify your account. Valid for 10 minutes.'
    ],
    
    'invoice_reminder_sms' => [
        'subject' => 'Invoice Reminder',
        'content' => 'Invoice {{invoice_number}} for ${{amount}} is due on {{due_date}}. Please pay on time.'
    ]
];

// Add Email Templates
foreach ($emailTemplates as $key => $template) {
    Setting::updateOrCreate(
        ['key' => 'template_' . $key],
        ['value' => json_encode($template)]
    );
    echo "Added email template: {$key}\n";
}

// Add SMS Templates
foreach ($smsTemplates as $key => $template) {
    Setting::updateOrCreate(
        ['key' => 'template_' . $key],
        ['value' => json_encode($template)]
    );
    echo "Added SMS template: {$key}\n";
}

echo "\nAll templates added successfully!\n"; 
<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

// Create welcome email template
Setting::updateOrCreate(
    ['key' => 'template_welcome_email'],
    ['value' => json_encode([
        'subject' => 'Welcome to HRMS - Your Account is Ready!',
        'content' => 'Dear {{name}},

Welcome to HRMS! Your account has been successfully created.

Your login details:
Email: {{email}}

We are excited to have you on board. If you have any questions, please don\'t hesitate to contact our support team.

Best regards,
The HRMS Team'
    ])]
);

// Create account setup guide email template
Setting::updateOrCreate(
    ['key' => 'template_account_setup_guide_email'],
    ['value' => json_encode([
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
    ])]
);

// Create features overview email template
Setting::updateOrCreate(
    ['key' => 'template_features_overview_email'],
    ['value' => json_encode([
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
    ])]
);

// Create subscription info email template
Setting::updateOrCreate(
    ['key' => 'template_subscription_info_email'],
    ['value' => json_encode([
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
    ])]
);

echo "Email templates created successfully!\n"; 
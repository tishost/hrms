<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // Free Plan
        SubscriptionPlan::create([
            'name' => 'Free',
            'price' => 0.00,
            'properties_limit' => 1,
            'units_limit' => 4,
            'tenants_limit' => 10,
            'sms_notification' => false,
            'is_active' => true,
            'features' => [
                'Basic Property Management',
                'Limited Units',
                'Basic Reports',
                'Email Support'
            ]
        ]);

        // Lite Plan
        SubscriptionPlan::create([
            'name' => 'Lite',
            'price' => 999.00,
            'properties_limit' => 2,
            'units_limit' => 30,
            'tenants_limit' => 50,
            'sms_notification' => true,
            'is_active' => true,
            'features' => [
                'Property Management',
                'SMS Notifications',
                'Advanced Reports',
                'Priority Support',
                'API Access'
            ]
        ]);

        // Advance Plan
        SubscriptionPlan::create([
            'name' => 'Advance',
            'price' => 1999.00,
            'properties_limit' => -1, // Unlimited
            'units_limit' => -1, // Unlimited
            'tenants_limit' => -1, // Unlimited
            'sms_notification' => true,
            'is_active' => true,
            'features' => [
                'Unlimited Properties',
                'Unlimited Units',
                'Unlimited Tenants',
                'SMS Notifications',
                'Custom Reports',
                'White Label',
                'Dedicated Support',
                'Full API Access',
                'Custom Branding'
            ]
        ]);
    }
}

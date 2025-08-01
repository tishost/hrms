<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // Free Plan
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Free'],
            [
                'price' => 0,
                'properties_limit' => 1,
                'units_limit' => 4,
                'tenants_limit' => 10,
                'sms_notification' => false,
                'sms_credit' => 0,
                'is_active' => true,
                'is_popular' => false,
                'features' => [
                    'Basic Property Management',
                    'Limited Units',
                    'Basic Reports',
                    'Email Support',
                    'SMS Notifications',
                    'Advanced Reports',
                    'Priority Support',
                    'API Access'
                ],
                'features_css' => [
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-times text-red-500',
                    'fas fa-times text-red-500',
                    'fas fa-times text-red-500',
                    'fas fa-times text-red-500'
                ]
            ]
        );

        // Lite Plan
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Lite'],
            [
                'price' => 999,
                'properties_limit' => 2,
                'units_limit' => 30,
                'tenants_limit' => 50,
                'sms_notification' => true,
                'sms_credit' => 1200,
                'is_active' => true,
                'is_popular' => true,
                'features' => [
                    'Property Management',
                    'SMS Notifications (1200 Credits)',
                    'Advanced Reports',
                    'Priority Support',
                    'API Access',
                    'Unlimited Properties',
                    'White Label',
                    'Custom Branding',
                    'Dedicated Support'
                ],
                'features_css' => [
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-times text-red-500',
                    'fas fa-times text-red-500',
                    'fas fa-times text-red-500',
                    'fas fa-times text-red-500'
                ]
            ]
        );

        // Advance Plan
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Advance'],
            [
                'price' => 1999,
                'properties_limit' => -1, // Unlimited
                'units_limit' => -1, // Unlimited
                'tenants_limit' => -1, // Unlimited
                'sms_notification' => true,
                'sms_credit' => 5000,
                'is_active' => true,
                'is_popular' => false,
                'features' => [
                    'Unlimited Properties',
                    'Unlimited Units',
                    'Unlimited Tenants',
                    'SMS Notifications (5000 Credits)',
                    'Custom Reports',
                    'White Label',
                    'Dedicated Support',
                    'Full API Access',
                    'Custom Branding'
                ],
                'features_css' => [
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500',
                    'fas fa-check text-green-500'
                ]
            ]
        );
    }
}

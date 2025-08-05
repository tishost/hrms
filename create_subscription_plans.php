<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SubscriptionPlan;

try {
    echo "=== Creating Subscription Plans with Billing Cycles ===\n\n";

    // Clear existing plans
    SubscriptionPlan::truncate();
    echo "✅ Cleared existing plans\n\n";

    // Create Free Plan (Lifetime)
    $freePlan = SubscriptionPlan::create([
        'name' => 'Free Plan',
        'price' => 0,
        'billing_cycle' => 'lifetime',
        'duration_days' => 36500, // 100 years
        'properties_limit' => 1,
        'units_limit' => 4,
        'tenants_limit' => 10,
        'sms_notification' => false,
        'sms_credit' => 0,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Basic property management',
            'Up to 1 property',
            'Up to 4 units',
            'Up to 10 tenants',
            'Email notifications only'
        ]
    ]);
    echo "✅ Created Free Plan (Lifetime)\n";

    // Create Basic Plan (Monthly)
    $basicMonthly = SubscriptionPlan::create([
        'name' => 'Basic Plan',
        'price' => 999,
        'billing_cycle' => 'monthly',
        'duration_days' => 30,
        'properties_limit' => 2,
        'units_limit' => 10,
        'tenants_limit' => 25,
        'sms_notification' => true,
        'sms_credit' => 50,
        'is_active' => true,
        'is_popular' => true,
        'features' => [
            'Advanced property management',
            'Up to 2 properties',
            'Up to 10 units',
            'Up to 25 tenants',
            'SMS notifications',
            '50 SMS credits/month'
        ]
    ]);
    echo "✅ Created Basic Plan (Monthly)\n";

    // Create Basic Plan (Yearly)
    $basicYearly = SubscriptionPlan::create([
        'name' => 'Basic Plan',
        'price' => 9990,
        'billing_cycle' => 'yearly',
        'duration_days' => 365,
        'properties_limit' => 2,
        'units_limit' => 10,
        'tenants_limit' => 25,
        'sms_notification' => true,
        'sms_credit' => 600,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Advanced property management',
            'Up to 2 properties',
            'Up to 10 units',
            'Up to 25 tenants',
            'SMS notifications',
            '600 SMS credits/year (2 months free)'
        ]
    ]);
    echo "✅ Created Basic Plan (Yearly)\n";

    // Create Basic Plan (Lifetime)
    $basicLifetime = SubscriptionPlan::create([
        'name' => 'Basic Plan',
        'price' => 19990,
        'billing_cycle' => 'lifetime',
        'duration_days' => 36500,
        'properties_limit' => 2,
        'units_limit' => 10,
        'tenants_limit' => 25,
        'sms_notification' => true,
        'sms_credit' => 1000,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Advanced property management',
            'Up to 2 properties',
            'Up to 10 units',
            'Up to 25 tenants',
            'SMS notifications',
            '1000 SMS credits (lifetime)'
        ]
    ]);
    echo "✅ Created Basic Plan (Lifetime)\n";

    // Create Pro Plan (Monthly)
    $proMonthly = SubscriptionPlan::create([
        'name' => 'Pro Plan',
        'price' => 1999,
        'billing_cycle' => 'monthly',
        'duration_days' => 30,
        'properties_limit' => 5,
        'units_limit' => 30,
        'tenants_limit' => 100,
        'sms_notification' => true,
        'sms_credit' => 200,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Professional property management',
            'Up to 5 properties',
            'Up to 30 units',
            'Up to 100 tenants',
            'SMS notifications',
            '200 SMS credits/month',
            'Advanced reporting'
        ]
    ]);
    echo "✅ Created Pro Plan (Monthly)\n";

    // Create Pro Plan (Yearly)
    $proYearly = SubscriptionPlan::create([
        'name' => 'Pro Plan',
        'price' => 19990,
        'billing_cycle' => 'yearly',
        'duration_days' => 365,
        'properties_limit' => 5,
        'units_limit' => 30,
        'tenants_limit' => 100,
        'sms_notification' => true,
        'sms_credit' => 2400,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Professional property management',
            'Up to 5 properties',
            'Up to 30 units',
            'Up to 100 tenants',
            'SMS notifications',
            '2400 SMS credits/year (2 months free)',
            'Advanced reporting'
        ]
    ]);
    echo "✅ Created Pro Plan (Yearly)\n";

    // Create Pro Plan (Lifetime)
    $proLifetime = SubscriptionPlan::create([
        'name' => 'Pro Plan',
        'price' => 39990,
        'billing_cycle' => 'lifetime',
        'duration_days' => 36500,
        'properties_limit' => 5,
        'units_limit' => 30,
        'tenants_limit' => 100,
        'sms_notification' => true,
        'sms_credit' => 5000,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Professional property management',
            'Up to 5 properties',
            'Up to 30 units',
            'Up to 100 tenants',
            'SMS notifications',
            '5000 SMS credits (lifetime)',
            'Advanced reporting'
        ]
    ]);
    echo "✅ Created Pro Plan (Lifetime)\n";

    // Create Enterprise Plan (Monthly)
    $enterpriseMonthly = SubscriptionPlan::create([
        'name' => 'Enterprise Plan',
        'price' => 4999,
        'billing_cycle' => 'monthly',
        'duration_days' => 30,
        'properties_limit' => -1, // Unlimited
        'units_limit' => -1, // Unlimited
        'tenants_limit' => -1, // Unlimited
        'sms_notification' => true,
        'sms_credit' => 1000,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Enterprise property management',
            'Unlimited properties',
            'Unlimited units',
            'Unlimited tenants',
            'SMS notifications',
            '1000 SMS credits/month',
            'Advanced reporting',
            'Priority support'
        ]
    ]);
    echo "✅ Created Enterprise Plan (Monthly)\n";

    // Create Enterprise Plan (Yearly)
    $enterpriseYearly = SubscriptionPlan::create([
        'name' => 'Enterprise Plan',
        'price' => 49990,
        'billing_cycle' => 'yearly',
        'duration_days' => 365,
        'properties_limit' => -1, // Unlimited
        'units_limit' => -1, // Unlimited
        'tenants_limit' => -1, // Unlimited
        'sms_notification' => true,
        'sms_credit' => 12000,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Enterprise property management',
            'Unlimited properties',
            'Unlimited units',
            'Unlimited tenants',
            'SMS notifications',
            '12000 SMS credits/year (2 months free)',
            'Advanced reporting',
            'Priority support'
        ]
    ]);
    echo "✅ Created Enterprise Plan (Yearly)\n";

    // Create Enterprise Plan (Lifetime)
    $enterpriseLifetime = SubscriptionPlan::create([
        'name' => 'Enterprise Plan',
        'price' => 99990,
        'billing_cycle' => 'lifetime',
        'duration_days' => 36500,
        'properties_limit' => -1, // Unlimited
        'units_limit' => -1, // Unlimited
        'tenants_limit' => -1, // Unlimited
        'sms_notification' => true,
        'sms_credit' => 25000,
        'is_active' => true,
        'is_popular' => false,
        'features' => [
            'Enterprise property management',
            'Unlimited properties',
            'Unlimited units',
            'Unlimited tenants',
            'SMS notifications',
            '25000 SMS credits (lifetime)',
            'Advanced reporting',
            'Priority support'
        ]
    ]);
    echo "✅ Created Enterprise Plan (Lifetime)\n";

    echo "\n=== SUMMARY ===\n";
    $plans = SubscriptionPlan::all();
    foreach ($plans as $plan) {
        echo sprintf(
            "%-20s | %-10s | %s\n",
            $plan->name,
            $plan->billing_cycle,
            $plan->formatted_price_with_cycle
        );
    }

    echo "\n✅ All subscription plans created successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
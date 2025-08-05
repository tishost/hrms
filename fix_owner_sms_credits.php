<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Owner;
use App\Models\OwnerSubscription;
use App\Models\SmsLog;
use App\Helpers\NotificationHelper;

try {
    // Get test user
    $user = User::where('phone', '8801700000000')->first();
    if (!$user) {
        echo "Test user not found!\n";
        exit;
    }

    $owner = Owner::where('user_id', $user->id)->first();
    if (!$owner) {
        echo "Owner not found!\n";
        exit;
    }

    $subscription = $owner->subscription;
    if (!$subscription) {
        echo "Subscription not found!\n";
        exit;
    }

    echo "=== BEFORE SMS TEST ===\n";
    echo "Owner: " . $owner->name . "\n";
    echo "SMS Credits: " . $subscription->sms_credits . "\n";
    echo "Used Credits: " . $subscription->used_sms_credits . "\n";
    echo "Remaining: " . ($subscription->sms_credits - $subscription->used_sms_credits) . "\n";

    // Test welcome SMS
    echo "\n=== TESTING WELCOME SMS ===\n";
    $result = NotificationHelper::sendComprehensiveWelcome($user);
    
    echo "SMS Result: ";
    if (isset($result['sms'])) {
        print_r($result['sms']);
    } else {
        echo "No SMS sent\n";
    }

    // Refresh subscription data
    $subscription->refresh();

    echo "\n=== AFTER SMS TEST ===\n";
    echo "SMS Credits: " . $subscription->sms_credits . "\n";
    echo "Used Credits: " . $subscription->used_sms_credits . "\n";
    echo "Remaining: " . ($subscription->sms_credits - $subscription->used_sms_credits) . "\n";

    echo "\n=== ANALYSIS ===\n";
    if ($subscription->used_sms_credits > 0) {
        echo "❌ PROBLEM: Owner SMS credits are being deducted for system SMS!\n";
        echo "This should NOT happen for welcome SMS.\n";
    } else {
        echo "✅ GOOD: No credits deducted for welcome SMS.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
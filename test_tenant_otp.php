<?php

// Test Tenant OTP System
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\TenantOtp;
use App\Models\User;

// Test mobile number (change this to a tenant that exists in your database)
$testMobile = '01712345678'; // Change this to an existing tenant mobile

echo "=== Tenant OTP Test ===\n";
echo "Testing mobile: $testMobile\n\n";

// 1. Check if tenant exists
echo "1. Checking if tenant exists...\n";
$tenant = Tenant::where('mobile', $testMobile)->first();

if (!$tenant) {
    echo "❌ Tenant not found with mobile: $testMobile\n";
    echo "Please add a tenant with this mobile number first.\n";
    exit;
}

echo "✅ Tenant found: {$tenant->first_name} {$tenant->last_name}\n";
echo "   Property: " . ($tenant->property ? $tenant->property->name : 'N/A') . "\n";
echo "   Unit: " . ($tenant->unit ? $tenant->unit->name : 'N/A') . "\n\n";

// 2. Check if user already exists
echo "2. Checking if user already exists...\n";
$existingUser = User::where('mobile', $testMobile)->first();

if ($existingUser) {
    echo "❌ User already exists with mobile: $testMobile\n";
    echo "   User ID: {$existingUser->id}\n";
    echo "   Name: {$existingUser->name}\n";
    echo "   Roles: " . implode(', ', $existingUser->getRoleNames()->toArray()) . "\n\n";
} else {
    echo "✅ No existing user found - can proceed with registration\n\n";
}

// 3. Check existing OTP records
echo "3. Checking existing OTP records...\n";
$existingOtps = TenantOtp::where('mobile', $testMobile)->get();

if ($existingOtps->count() > 0) {
    echo "Found {$existingOtps->count()} OTP record(s):\n";
    foreach ($existingOtps as $otp) {
        echo "   - OTP: {$otp->otp}\n";
        echo "     Expires: {$otp->expires_at}\n";
        echo "     Used: " . ($otp->is_used ? 'Yes' : 'No') . "\n";
        echo "     Valid: " . ($otp->isValid() ? 'Yes' : 'No') . "\n\n";
    }
} else {
    echo "✅ No existing OTP records found\n\n";
}

// 4. Generate test OTP
echo "4. Generating test OTP...\n";
$otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

$otpRecord = TenantOtp::updateOrCreate(
    ['mobile' => $testMobile],
    [
        'otp' => $otp,
        'expires_at' => now()->addMinutes(10),
        'is_used' => false
    ]
);

echo "✅ OTP generated and saved:\n";
echo "   OTP: $otp\n";
echo "   Expires: {$otpRecord->expires_at}\n";
echo "   ID: {$otpRecord->id}\n\n";

// 5. Test OTP verification
echo "5. Testing OTP verification...\n";
$testOtp = $otp; // Use the same OTP

$otpRecord = TenantOtp::where('mobile', $testMobile)
    ->where('otp', $testOtp)
    ->first();

if ($otpRecord && $otpRecord->isValid()) {
    echo "✅ OTP verification successful\n";
    echo "   Marking OTP as used...\n";
    $otpRecord->update(['is_used' => true]);
    echo "   ✅ OTP marked as used\n\n";
} else {
    echo "❌ OTP verification failed\n";
    if (!$otpRecord) {
        echo "   - OTP record not found\n";
    } else {
        echo "   - OTP is used: " . ($otpRecord->is_used ? 'Yes' : 'No') . "\n";
        echo "   - OTP is expired: " . ($otpRecord->expires_at < now() ? 'Yes' : 'No') . "\n";
    }
    echo "\n";
}

echo "=== Test Complete ===\n";
echo "If everything shows ✅, your OTP system is working correctly.\n";
echo "Use the OTP '$otp' in your Flutter app for testing.\n";

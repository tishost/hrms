<?php

/**
 * Test Script for Tenant Registration Process
 * This script tests the complete tenant registration flow
 */

require_once 'vendor/autoload.php';

use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantOtp;
use Illuminate\Support\Facades\Hash;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Tenant Registration Test Script ===\n\n";

// Test mobile number
$testMobile = '01712345678';

try {
    // Step 1: Check if tenant exists
    echo "Step 1: Checking if tenant exists...\n";
    $tenant = Tenant::where('mobile', $testMobile)->first();

    if (!$tenant) {
        echo "❌ Tenant not found with mobile: $testMobile\n";
        echo "Please create a tenant first in the admin panel.\n";
        exit(1);
    }

    echo "✅ Tenant found: {$tenant->first_name} {$tenant->last_name}\n";
    echo "   Property: " . ($tenant->property ? $tenant->property->name : 'N/A') . "\n";
    echo "   Unit: " . ($tenant->unit ? $tenant->unit->name : 'N/A') . "\n\n";

    // Step 2: Check if user already exists
    echo "Step 2: Checking if user already exists...\n";
    $existingUser = User::where('phone', $testMobile)->first();

    if ($existingUser) {
        echo "❌ User already exists with mobile: $testMobile\n";
        echo "   User ID: {$existingUser->id}\n";
        echo "   Name: {$existingUser->name}\n";
        exit(1);
    }

    echo "✅ No existing user found\n\n";

    // Step 3: Create OTP
    echo "Step 3: Creating OTP...\n";
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $otpRecord = TenantOtp::updateOrCreate(
        ['mobile' => $testMobile],
        [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false
        ]
    );

    echo "✅ OTP created: $otp\n";
    echo "   OTP ID: {$otpRecord->id}\n";
    echo "   Expires: {$otpRecord->expires_at}\n\n";

    // Step 4: Verify OTP
    echo "Step 4: Verifying OTP...\n";
    $otpRecord = TenantOtp::where('mobile', $testMobile)
        ->where('otp', $otp)
        ->first();

    if (!$otpRecord || !$otpRecord->isValid()) {
        echo "❌ OTP verification failed\n";
        exit(1);
    }

    // Mark OTP as used
    $otpRecord->update(['is_used' => true]);
    echo "✅ OTP verified and marked as used\n\n";

    // Step 5: Create user
    echo "Step 5: Creating user...\n";
    $user = User::create([
        'name' => trim(($tenant->first_name ?? '') . ' ' . ($tenant->last_name ?? '')),
        'phone' => $tenant->mobile,
        'email' => $tenant->email,
        'password' => Hash::make('password123'),
        'tenant_id' => $tenant->id,
        'owner_id' => $tenant->owner_id,
    ]);

    echo "✅ User created successfully\n";
    echo "   User ID: {$user->id}\n";
    echo "   Name: {$user->name}\n";
    echo "   Phone: {$user->phone}\n";
    echo "   Tenant ID: {$user->tenant_id}\n";
    echo "   Owner ID: {$user->owner_id}\n\n";

    // Step 6: Assign role
    echo "Step 6: Assigning tenant role...\n";
    $user->assignRole('tenant');
    echo "✅ Tenant role assigned\n\n";

    // Step 7: Generate token
    echo "Step 7: Generating auth token...\n";
    $token = $user->createToken('auth_token')->plainTextToken;
    echo "✅ Auth token generated\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";

    // Step 8: Verify final state
    echo "Step 8: Verifying final state...\n";
    $finalUser = User::where('phone', $testMobile)->first();
    $finalOtp = TenantOtp::where('mobile', $testMobile)->first();

    echo "✅ Final verification complete:\n";
    echo "   User exists: " . ($finalUser ? 'Yes' : 'No') . "\n";
    echo "   User has tenant role: " . ($finalUser->hasRole('tenant') ? 'Yes' : 'No') . "\n";
    echo "   OTP is used: " . ($finalOtp->is_used ? 'Yes' : 'No') . "\n";
    echo "   User can authenticate: " . (auth()->attempt(['phone' => $testMobile, 'password' => 'password123']) ? 'Yes' : 'No') . "\n\n";

    echo "🎉 Tenant registration test completed successfully!\n";
    echo "Test mobile: $testMobile\n";
    echo "Test password: password123\n";
    echo "You can now test the mobile app with these credentials.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

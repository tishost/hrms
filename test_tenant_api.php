<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Owner;

echo "=== Testing Tenant API ===\n\n";

// Get a test user with owner
$user = User::whereNotNull('owner_id')->first();

if (!$user) {
    // Try to find any user and set owner_id
    $user = User::first();
    if ($user) {
        $owner = Owner::first();
        if ($owner) {
            $user->update(['owner_id' => $owner->id]);
            echo "Updated user with owner_id: " . $owner->id . "\n";
        }
    }
}

if (!$user) {
    echo "No user with owner found.\n";
    exit;
}

echo "Using user: " . $user->email . "\n";
echo "Owner ID: " . $user->owner_id . "\n\n";

// Create a test token
$token = $user->createToken('test-token')->plainTextToken;
echo "Token: " . substr($token, 0, 50) . "...\n\n";

// Test data with all fields
$testData = [
    'first_name' => 'Test',
    'last_name' => 'Tenant',
    'gender' => 'Male',
    'mobile' => '01712345678',
    'alt_mobile' => '01812345678',
    'email' => 'test@example.com',
    'nid_number' => '1234567890',
    'address' => 'Test Address',
    'city' => 'Dhaka',
    'state' => 'Dhaka Division',
    'zip' => '1200',
    'country' => 'Bangladesh',
    'occupation' => 'Service',
    'company_name' => 'Test Company',
    'college_university' => 'Test University',
    'business_name' => 'Test Business',
    'is_driver' => true,
    'driver_name' => 'Test Driver',
    'family_types' => 'Spouse,Child',
    'child_qty' => 2,
    'total_family_member' => 4,
    'property_id' => 1,
    'unit_id' => 1,
    'advance_amount' => 5000,
    'start_month' => '08-2025',
    'frequency' => 'Monthly',
    'remarks' => 'Test remarks',
];

echo "Test data:\n";
foreach ($testData as $key => $value) {
    echo "$key: $value\n";
}

echo "\n=== Making API Call ===\n";

// Make API call
$response = app('Illuminate\Http\Request')->create('/api/tenants', 'POST', $testData);
$response->headers->set('Authorization', 'Bearer ' . $token);
$response->headers->set('Accept', 'application/json');
$response->headers->set('Content-Type', 'application/json');

$kernel = app('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle($response);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Body: " . $response->getContent() . "\n";

echo "\n=== Test Complete ===\n";

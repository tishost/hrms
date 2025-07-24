<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Owner;
use App\Models\Tenant;

echo "=== Testing Owner User Detection ===\n\n";

// Test with owner user
$ownerEmail = 'owner@hrms.com'; // Replace with actual owner email
echo "Testing owner email: $ownerEmail\n\n";

$user = User::where('email', $ownerEmail)->first();

if ($user) {
    echo "✅ User found:\n";
    echo "- ID: {$user->id}\n";
    echo "- Email: {$user->email}\n";
    echo "- Name: {$user->name}\n";
    echo "- Phone: {$user->phone}\n";
    echo "- Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";

    // Check owner relationship
    if ($user->owner) {
        echo "\n✅ Owner relationship found:\n";
        echo "- Owner ID: {$user->owner->id}\n";
        echo "- Owner Name: {$user->owner->name}\n";
        echo "- Owner Email: {$user->owner->email}\n";
        echo "- Owner Phone: {$user->owner->phone}\n";
        echo "- Owner Mobile: {$user->owner->mobile}\n";
        echo "- First Name: {$user->owner->first_name}\n";
        echo "- Last Name: {$user->owner->last_name}\n";
    } else {
        echo "\n❌ No owner relationship found\n";
    }

    // Check tenant relationship
    if ($user->tenant) {
        echo "\n✅ Tenant relationship found:\n";
        echo "- Tenant ID: {$user->tenant->id}\n";
        echo "- Tenant Name: {$user->tenant->first_name} {$user->tenant->last_name}\n";
    } else {
        echo "\n❌ No tenant relationship found\n";
    }

    // Test AuthController getUserProfile method
    echo "\n=== Testing AuthController getUserProfile ===\n";

    try {
        // Create a mock request
        $request = new \Illuminate\Http\Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $authController = new \App\Http\Controllers\Api\AuthController();
        $response = $authController->getUserProfile($request);
        $responseData = json_decode($response->getContent(), true);

        echo "Response Status: " . $response->getStatusCode() . "\n";
        echo "Response Data:\n";
        print_r($responseData);

        if (isset($responseData['user']['owner'])) {
            echo "\n✅ Owner data in response\n";
        } else {
            echo "\n❌ No owner data in response\n";
        }

        if (isset($responseData['user']['tenant'])) {
            echo "\n✅ Tenant data in response\n";
        } else {
            echo "\n❌ No tenant data in response\n";
        }

    } catch (Exception $e) {
        echo "❌ Error testing getUserProfile: " . $e->getMessage() . "\n";
    }

    // Test OwnerController profile method
    echo "\n=== Testing OwnerController profile ===\n";

    try {
        $request = new \Illuminate\Http\Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $ownerController = new \App\Http\Controllers\Api\OwnerController();
        $response = $ownerController->profile($request);
        $responseData = json_decode($response->getContent(), true);

        echo "Response Status: " . $response->getStatusCode() . "\n";
        echo "Response Data:\n";
        print_r($responseData);

    } catch (Exception $e) {
        echo "❌ Error testing OwnerController profile: " . $e->getMessage() . "\n";
    }

} else {
    echo "❌ User not found with email: $ownerEmail\n";

    // List all users with owner role
    echo "\n=== All Users with Owner Role ===\n";
    $ownerUsers = User::whereHas('roles', function($query) {
        $query->where('name', 'owner');
    })->get();

    foreach ($ownerUsers as $ownerUser) {
        echo "- ID: {$ownerUser->id}, Email: {$ownerUser->email}, Name: {$ownerUser->name}\n";
    }
}

echo "\n=== Test Complete ===\n";
?>

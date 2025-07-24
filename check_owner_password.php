<?php

// Check Owner Password
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Owner;

echo "=== Owner Password Check ===\n\n";

// Check in users table
echo "1. Checking in users table...\n";
$user = User::where('email', 'owner@hrms.com')->first();

if ($user) {
    echo "✅ User found in users table:\n";
    echo "   ID: {$user->id}\n";
    echo "   Name: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Phone: {$user->phone}\n";
    echo "   Created: {$user->created_at}\n";
    echo "   Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n\n";
} else {
    echo "❌ User not found in users table\n\n";
}

// Check in owners table
echo "2. Checking in owners table...\n";
$owner = Owner::where('email', 'owner@hrms.com')->first();

if ($owner) {
    echo "✅ Owner found in owners table:\n";
    echo "   ID: {$owner->id}\n";
    echo "   Name: {$owner->first_name} {$owner->last_name}\n";
    echo "   Email: {$owner->email}\n";
    echo "   Mobile: {$owner->mobile}\n";
    echo "   Created: {$owner->created_at}\n\n";
} else {
    echo "❌ Owner not found in owners table\n\n";
}

// Check all users with owner role
echo "3. Checking all users with owner role...\n";
$ownerUsers = User::role('owner')->get();

if ($ownerUsers->count() > 0) {
    echo "Found {$ownerUsers->count()} user(s) with owner role:\n";
    foreach ($ownerUsers as $ownerUser) {
        echo "   - ID: {$ownerUser->id}\n";
        echo "     Name: {$ownerUser->name}\n";
        echo "     Email: {$ownerUser->email}\n";
        echo "     Phone: {$ownerUser->phone}\n";
        echo "     Created: {$ownerUser->created_at}\n\n";
    }
} else {
    echo "❌ No users found with owner role\n\n";
}

// Check all owners
echo "4. Checking all owners...\n";
$allOwners = Owner::all();

if ($allOwners->count() > 0) {
    echo "Found {$allOwners->count()} owner(s):\n";
    foreach ($allOwners as $owner) {
        echo "   - ID: {$owner->id}\n";
        echo "     Name: {$owner->first_name} {$owner->last_name}\n";
        echo "     Email: {$owner->email}\n";
        echo "     Mobile: {$owner->mobile}\n";
        echo "     Created: {$owner->created_at}\n\n";
    }
} else {
    echo "❌ No owners found\n\n";
}

echo "=== Check Complete ===\n";
echo "If you need to reset password, use the reset command.\n";

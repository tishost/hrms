<?php

// Reset Owner Password
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Owner Password Reset ===\n\n";

// Find the owner user
$user = User::where('email', 'owner@hrms.com')->first();

if (!$user) {
    echo "❌ User not found with email: owner@hrms.com\n";
    exit;
}

echo "✅ User found:\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Phone: {$user->phone}\n";
echo "   Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n\n";

// Set new password
$newPassword = '123456'; // Simple password for testing
$user->password = Hash::make($newPassword);
$user->save();

echo "✅ Password reset successfully!\n";
echo "   New password: $newPassword\n";
echo "   Email: owner@hrms.com\n";
echo "   Phone: {$user->phone}\n\n";

echo "=== Login Credentials ===\n";
echo "Email/Phone: owner@hrms.com or {$user->phone}\n";
echo "Password: $newPassword\n\n";

echo "=== Test Login ===\n";
echo "You can now login with:\n";
echo "1. Email: owner@hrms.com\n";
echo "2. Phone: {$user->phone}\n";
echo "3. Password: $newPassword\n\n";

echo "=== Reset Complete ===\n";

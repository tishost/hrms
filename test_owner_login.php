<?php

// Test Owner Login
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Owner Login Test ===\n\n";

// Test credentials
$email = 'owner@hrms.com';
$phone = '01718262530';
$password = '123456';

echo "Testing credentials:\n";
echo "Email: $email\n";
echo "Phone: $phone\n";
echo "Password: $password\n\n";

// 1. Check if user exists
echo "1. Checking if user exists...\n";
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found with email: $email\n";
    exit;
}

echo "✅ User found:\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Phone: {$user->phone}\n";
echo "   Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n\n";

// 2. Check password
echo "2. Checking password...\n";
if (Hash::check($password, $user->password)) {
    echo "✅ Password is correct!\n\n";
} else {
    echo "❌ Password is incorrect!\n";
    echo "   Current password hash: {$user->password}\n";
    echo "   Testing password: $password\n\n";

    // Reset password
    echo "3. Resetting password...\n";
    $user->password = Hash::make($password);
    $user->save();
    echo "✅ Password reset to: $password\n\n";
}

// 3. Test API login
echo "4. Testing API login...\n";

// Simulate API request
$request = new \Illuminate\Http\Request();
$request->merge([
    'mobile' => $phone,
    'password' => $password
]);

try {
    $controller = new \App\Http\Controllers\Api\AuthController();
    $response = $controller->login($request);

    echo "✅ API login successful!\n";
    echo "Response status: " . $response->getStatusCode() . "\n";

    $data = json_decode($response->getContent(), true);
    echo "Response data:\n";
    print_r($data);

} catch (Exception $e) {
    echo "❌ API login failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

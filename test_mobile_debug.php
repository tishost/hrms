<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Mobile App Debug Test ===\n\n";

// Test with owner user
$ownerEmail = 'owner@hrms.com';
echo "Testing with owner: $ownerEmail\n\n";

$user = \App\Models\User::where('email', $ownerEmail)->first();

if ($user) {
    echo "✅ User found:\n";
    echo "- ID: {$user->id}\n";
    echo "- Email: {$user->email}\n";
    echo "- Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";

    // Create a token for testing
    $token = $user->createToken('test-token')->plainTextToken;
    echo "- Token: " . substr($token, 0, 20) . "...\n\n";

    // Test user profile API
    echo "=== Testing User Profile API ===\n";

    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $authController = new \App\Http\Controllers\Api\AuthController();
    $response = $authController->getUserProfile($request);
    $responseData = json_decode($response->getContent(), true);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response:\n";
    print_r($responseData);

    // Test owner profile API
    echo "\n=== Testing Owner Profile API ===\n";

    $ownerController = new \App\Http\Controllers\Api\OwnerController();
    $response = $ownerController->profile($request);
    $responseData = json_decode($response->getContent(), true);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response:\n";
    print_r($responseData);

    // Test owner PDF API
    echo "\n=== Testing Owner PDF API ===\n";

    $invoiceId = 2; // INV-2025-0002
    $response = $ownerController->downloadInvoicePDF($request, $invoiceId);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Content-Length: " . strlen($response->getContent()) . " bytes\n";

    // Test tenant PDF API (should fail for owner)
    echo "\n=== Testing Tenant PDF API (should fail) ===\n";

    $tenantController = new \App\Http\Controllers\Api\TenantController();
    $response = $tenantController->downloadInvoicePDF($request, $invoiceId);
    $responseData = json_decode($response->getContent(), true);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Response:\n";
    print_r($responseData);

    // Mobile app simulation
    echo "\n=== Mobile App Simulation ===\n";
    echo "Expected Flow:\n";
    echo "1. Call /api/user/profile\n";
    echo "2. Get user data with owner info\n";
    echo "3. Detect user as 'owner'\n";
    echo "4. Call /api/owner/invoices/{id}/pdf-file\n";
    echo "5. Get PDF response\n\n";

    echo "Current Issue:\n";
    echo "❌ Mobile app calls /api/tenant/invoices/{id}/pdf-file\n";
    echo "❌ Gets 'User is not a tenant' error\n\n";

    echo "Debug Info for Mobile App:\n";
    echo "API Base URL: http://103.98.76.11/api\n";
    echo "User Profile: http://103.98.76.11/api/user/profile\n";
    echo "Owner Profile: http://103.98.76.11/api/owner/profile\n";
    echo "Owner PDF: http://103.98.76.11/api/owner/invoices/{id}/pdf-file\n";
    echo "Tenant PDF: http://103.98.76.11/api/tenant/invoices/{id}/pdf-file\n";

} else {
    echo "❌ User not found: $ownerEmail\n";
}

echo "\n=== Test Complete ===\n";
?>

<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Both User Types ===\n\n";

// Test with owner user
echo "=== Testing Owner User ===\n";
$ownerEmail = 'owner@hrms.com';
$ownerUser = \App\Models\User::where('email', $ownerEmail)->first();

if ($ownerUser) {
    echo "✅ Owner User found:\n";
    echo "- ID: {$ownerUser->id}\n";
    echo "- Email: {$ownerUser->email}\n";
    echo "- Roles: " . $ownerUser->roles->pluck('name')->implode(', ') . "\n";

    // Test owner PDF API
    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function () use ($ownerUser) {
        return $ownerUser;
    });

    $ownerController = new \App\Http\Controllers\Api\OwnerController();
    $response = $ownerController->downloadInvoicePDF($request, 2);

    echo "Owner PDF API Status: " . $response->getStatusCode() . "\n";
    echo "Owner PDF Content Length: " . strlen($response->getContent()) . " bytes\n";

    // Test tenant PDF API (should fail for owner)
    $tenantController = new \App\Http\Controllers\Api\TenantController();
    $response = $tenantController->downloadInvoicePDF($request, 2);
    $responseData = json_decode($response->getContent(), true);

    echo "Tenant PDF API Status: " . $response->getStatusCode() . "\n";
    echo "Tenant PDF Response: " . json_encode($responseData) . "\n\n";
}

// Test with tenant user
echo "=== Testing Tenant User ===\n";
$tenantEmail = 'sam@djddnd.com';
$tenantUser = \App\Models\User::where('email', $tenantEmail)->first();

if ($tenantUser) {
    echo "✅ Tenant User found:\n";
    echo "- ID: {$tenantUser->id}\n";
    echo "- Email: {$tenantUser->email}\n";
    echo "- Roles: " . $tenantUser->roles->pluck('name')->implode(', ') . "\n";

    // Test tenant PDF API
    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function () use ($tenantUser) {
        return $tenantUser;
    });

    $tenantController = new \App\Http\Controllers\Api\TenantController();
    $response = $tenantController->downloadInvoicePDF($request, 2);

    echo "Tenant PDF API Status: " . $response->getStatusCode() . "\n";
    echo "Tenant PDF Content Length: " . strlen($response->getContent()) . " bytes\n";

    // Test owner PDF API (should fail for tenant)
    $ownerController = new \App\Http\Controllers\Api\OwnerController();
    $response = $ownerController->downloadInvoicePDF($request, 2);
    $responseData = json_decode($response->getContent(), true);

    echo "Owner PDF API Status: " . $response->getStatusCode() . "\n";
    echo "Owner PDF Response: " . json_encode($responseData) . "\n\n";
}

echo "=== Expected Results ===\n";
echo "Owner User:\n";
echo "- Owner PDF API: ✅ Should work (200 + PDF content)\n";
echo "- Tenant PDF API: ❌ Should fail (403 + 'User is not a tenant')\n\n";

echo "Tenant User:\n";
echo "- Tenant PDF API: ✅ Should work (200 + PDF content)\n";
echo "- Owner PDF API: ❌ Should fail (403 + 'User is not an owner')\n\n";

echo "=== Mobile App Flow ===\n";
echo "1. User clicks invoice\n";
echo "2. Call /api/user/profile to detect user type\n";
echo "3. Based on user type:\n";
echo "   - Owner: Call /api/owner/invoices/{id}/pdf-file\n";
echo "   - Tenant: Call /api/tenant/invoices/{id}/pdf-file\n";
echo "4. Display PDF\n\n";

echo "=== Test Complete ===\n";
?>

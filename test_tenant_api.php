<?php

// Test Tenant API
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Tenant;

echo "=== Tenant API Test ===\n\n";

// Get owner user
$owner = User::where('email', 'owner@hrms.com')->first();
if (!$owner) {
    echo "❌ Owner not found\n";
    exit;
}

echo "✅ Owner found: {$owner->name}\n\n";

// Get tenants for this owner
$tenants = Tenant::where('owner_id', $owner->owner->id)->get();

echo "Found {$tenants->count()} tenants\n\n";

foreach ($tenants as $tenant) {
    echo "=== Tenant ID: {$tenant->id} ===\n";
    echo "Name: {$tenant->first_name} {$tenant->last_name}\n";
    echo "Mobile: {$tenant->mobile}\n";
    echo "Family Types: ";

    if ($tenant->family_types) {
        if (is_array($tenant->family_types)) {
            echo implode(', ', $tenant->family_types);
        } else {
            echo $tenant->family_types;
        }
    } else {
        echo "null";
    }
    echo "\n";

    echo "Family Types Type: " . gettype($tenant->family_types) . "\n";
    echo "Family Types Raw: " . var_export($tenant->family_types, true) . "\n";
    echo "\n";
}

// Test API endpoint
echo "=== Testing API Endpoint ===\n";

$token = $owner->createToken('test-token')->plainTextToken;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://103.98.76.11/api/tenants');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response:\n$response\n\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['tenants']) && !empty($data['tenants'])) {
        $firstTenant = $data['tenants'][0];
        echo "First tenant from API:\n";
        echo "ID: {$firstTenant['id']}\n";
        echo "Name: {$firstTenant['name']}\n";
        echo "Family Types: " . var_export($firstTenant['family_types'], true) . "\n";
        echo "Family Types Type: " . gettype($firstTenant['family_types']) . "\n";
    }
}

echo "\n=== Test Complete ===\n";

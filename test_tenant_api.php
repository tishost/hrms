<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Tenant;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Tenant API...\n";

// Get a user with owner_id
$user = User::whereNotNull('owner_id')->first();

if (!$user) {
    echo "No user with owner_id found\n";
    exit;
}

echo "Found user: " . $user->email . " with owner_id: " . $user->owner_id . "\n";

// Get tenants for this owner
$tenants = Tenant::whereHas('unit.property', function($query) use ($user) {
    $query->where('owner_id', $user->owner_id);
})
->with(['unit.property'])
->get();

echo "Found " . $tenants->count() . " tenants for owner_id: " . $user->owner_id . "\n";

foreach ($tenants as $tenant) {
    echo "Tenant: " . $tenant->first_name . " " . $tenant->last_name . "\n";
    echo "  Mobile: " . $tenant->mobile . "\n";
    echo "  Unit: " . ($tenant->unit->name ?? 'No Unit') . "\n";
    echo "  Property: " . ($tenant->unit->property->name ?? 'No Property') . "\n";
    echo "  Property Owner ID: " . ($tenant->unit->property->owner_id ?? 'No Owner') . "\n";
    echo "---\n";
}

// Test the API response format
$apiResponse = [
    'tenants' => $tenants->map(function($tenant) {
        return [
            'id' => $tenant->id,
            'name' => $tenant->first_name . ' ' . $tenant->last_name,
            'mobile' => $tenant->mobile,
            'email' => $tenant->email,
            'property_name' => $tenant->unit->property->name ?? 'No Property',
            'unit_name' => $tenant->unit->name ?? 'No Unit',
            'rent' => $tenant->unit->rent ?? 0,
            'status' => $tenant->status ?? 'active',
            'created_at' => $tenant->created_at,
        ];
    })
];

echo "API Response:\n";
echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n";

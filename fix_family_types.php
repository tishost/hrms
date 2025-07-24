<?php

// Fix Family Types Data
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "=== Fix Family Types Data ===\n\n";

try {
    // Get all tenants
    $tenants = Tenant::all();

    echo "Found {$tenants->count()} tenants\n\n";

    foreach ($tenants as $tenant) {
        echo "Processing Tenant ID: {$tenant->id} - {$tenant->first_name} {$tenant->last_name}\n";
        echo "Original family_types: " . var_export($tenant->family_types, true) . "\n";

        // Reset to clean array
        $cleanFamilyTypes = ['Child', 'Parents', 'Spouse', 'Siblings', 'Others'];

        echo "Setting clean family_types: " . var_export($cleanFamilyTypes, true) . "\n";

        // Update the tenant
        $tenant->family_types = $cleanFamilyTypes;
        $tenant->save();

        echo "✅ Updated Tenant ID: {$tenant->id}\n\n";
    }

    echo "=== Fix Complete ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

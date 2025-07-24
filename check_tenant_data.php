<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;

echo "=== Tenant Database Data ===\n";

try {
    $tenants = Tenant::select('id', 'first_name', 'last_name', 'mobile', 'gender', 'occupation')->get();

    if ($tenants->count() > 0) {
        foreach ($tenants as $tenant) {
            echo "ID: {$tenant->id}\n";
            echo "First Name: '{$tenant->first_name}'\n";
            echo "Last Name: '{$tenant->last_name}'\n";
            echo "Mobile: '{$tenant->mobile}'\n";
            echo "Gender: '{$tenant->gender}'\n";
            echo "Occupation: '{$tenant->occupation}'\n";
            echo "---\n";
        }
    } else {
        echo "No tenants found in database\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== End ===\n";

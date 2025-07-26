<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Tenant Fields Test ===\n\n";

// Get the latest tenant
$latestTenant = Tenant::latest()->first();

if ($latestTenant) {
    echo "Latest Tenant ID: " . $latestTenant->id . "\n";
    echo "Latest Tenant Name: " . $latestTenant->first_name . " " . $latestTenant->last_name . "\n\n";

    echo "=== All Tenant Fields ===\n";
    echo "first_name: " . ($latestTenant->first_name ?? 'NULL') . "\n";
    echo "last_name: " . ($latestTenant->last_name ?? 'NULL') . "\n";
    echo "gender: " . ($latestTenant->gender ?? 'NULL') . "\n";
    echo "mobile: " . ($latestTenant->mobile ?? 'NULL') . "\n";
    echo "alt_mobile: " . ($latestTenant->alt_mobile ?? 'NULL') . "\n";
    echo "email: " . ($latestTenant->email ?? 'NULL') . "\n";
    echo "nid_number: " . ($latestTenant->nid_number ?? 'NULL') . "\n";
    echo "address: " . ($latestTenant->address ?? 'NULL') . "\n";
    echo "city: " . ($latestTenant->city ?? 'NULL') . "\n";
    echo "state: " . ($latestTenant->state ?? 'NULL') . "\n";
    echo "zip: " . ($latestTenant->zip ?? 'NULL') . "\n";
    echo "country: " . ($latestTenant->country ?? 'NULL') . "\n";
    echo "occupation: " . ($latestTenant->occupation ?? 'NULL') . "\n";
    echo "company_name: " . ($latestTenant->company_name ?? 'NULL') . "\n";
    echo "college_university: " . ($latestTenant->college_university ?? 'NULL') . "\n";
    echo "business_name: " . ($latestTenant->business_name ?? 'NULL') . "\n";
    echo "is_driver: " . ($latestTenant->is_driver ?? 'NULL') . "\n";
    echo "driver_name: " . ($latestTenant->driver_name ?? 'NULL') . "\n";
    echo "family_types: " . ($latestTenant->family_types ?? 'NULL') . "\n";
    echo "child_qty: " . ($latestTenant->child_qty ?? 'NULL') . "\n";
    echo "total_family_member: " . ($latestTenant->total_family_member ?? 'NULL') . "\n";
    echo "building_id: " . ($latestTenant->building_id ?? 'NULL') . "\n";
    echo "unit_id: " . ($latestTenant->unit_id ?? 'NULL') . "\n";
    echo "security_deposit: " . ($latestTenant->security_deposit ?? 'NULL') . "\n";
    echo "check_in_date: " . ($latestTenant->check_in_date ?? 'NULL') . "\n";
    echo "frequency: " . ($latestTenant->frequency ?? 'NULL') . "\n";
    echo "remarks: " . ($latestTenant->remarks ?? 'NULL') . "\n";
    echo "status: " . ($latestTenant->status ?? 'NULL') . "\n";
    echo "owner_id: " . ($latestTenant->owner_id ?? 'NULL') . "\n";

    echo "\n=== Database Schema ===\n";
    $columns = DB::select("DESCRIBE tenants");
    foreach ($columns as $column) {
        echo $column->Field . " - " . $column->Type . " - " . $column->Null . " - " . $column->Default . "\n";
    }

} else {
    echo "No tenants found in database.\n";
}

echo "\n=== Test Complete ===\n";

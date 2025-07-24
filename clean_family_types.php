<?php

// Clean Family Types Data
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "=== Clean Family Types Data ===\n\n";

try {
    // Get all tenants with corrupted family_types
    $tenants = Tenant::all();

    echo "Found {$tenants->count()} tenants\n\n";

    foreach ($tenants as $tenant) {
        echo "Processing Tenant ID: {$tenant->id} - {$tenant->first_name} {$tenant->last_name}\n";
        echo "Original family_types: " . var_export($tenant->family_types, true) . "\n";

        $cleanFamilyTypes = [];

        if ($tenant->family_types) {
            if (is_array($tenant->family_types)) {
                foreach ($tenant->family_types as $item) {
                    if (is_string($item)) {
                        // Try to decode JSON string
                        if (strpos($item, '[') === 0 && strpos($item, ']') === strlen($item) - 1) {
                            try {
                                $decoded = json_decode($item, true);
                                if (is_array($decoded)) {
                                    foreach ($decoded as $decodedItem) {
                                        if (is_string($decodedItem) && !empty(trim($decodedItem))) {
                                            $cleanFamilyTypes[] = trim($decodedItem);
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                                // If JSON decode fails, treat as regular string
                                if (!empty(trim($item))) {
                                    $cleanFamilyTypes[] = trim($item);
                                }
                            }
                        } else {
                            // Regular string
                            if (!empty(trim($item))) {
                                $cleanFamilyTypes[] = trim($item);
                            }
                        }
                    }
                }
            } else if (is_string($tenant->family_types)) {
                // Try to decode JSON string
                try {
                    $decoded = json_decode($tenant->family_types, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $item) {
                            if (is_string($item) && !empty(trim($item))) {
                                $cleanFamilyTypes[] = trim($item);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // If JSON decode fails, treat as comma-separated string
                    $parts = explode(',', $tenant->family_types);
                    foreach ($parts as $part) {
                        if (!empty(trim($part))) {
                            $cleanFamilyTypes[] = trim($part);
                        }
                    }
                }
            }
        }

        // Remove duplicates and empty values
        $cleanFamilyTypes = array_unique(array_filter($cleanFamilyTypes));

        echo "Cleaned family_types: " . var_export($cleanFamilyTypes, true) . "\n";

        // Update the tenant
        $tenant->family_types = $cleanFamilyTypes;
        $tenant->save();

        echo "✅ Updated Tenant ID: {$tenant->id}\n\n";
    }

    echo "=== Cleanup Complete ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== Dropping Settings Table ===\n\n";

    // 1. Check if settings table exists
    echo "1. Checking if settings table exists...\n";
    if (Schema::hasTable('settings')) {
        echo "✅ Settings table exists\n";
    } else {
        echo "❌ Settings table does not exist\n";
        exit;
    }

    // 2. Count records in both tables
    echo "\n2. Counting records...\n";
    $oldSettingsCount = Setting::count();
    $newSettingsCount = SystemSetting::count();
    
    echo "- Old settings table: {$oldSettingsCount} records\n";
    echo "- New system_settings table: {$newSettingsCount} records\n";

    // 3. Verify all data is migrated
    echo "\n3. Verifying data migration...\n";
    $oldSettings = Setting::all();
    $migratedCount = 0;
    $missingCount = 0;

    foreach ($oldSettings as $setting) {
        $exists = SystemSetting::where('key', $setting->key)->exists();
        if ($exists) {
            $migratedCount++;
        } else {
            echo "- Missing: {$setting->key}\n";
            $missingCount++;
        }
    }

    echo "\nMigration verification:\n";
    echo "- Total old settings: " . $oldSettings->count() . "\n";
    echo "- Successfully migrated: {$migratedCount}\n";
    echo "- Missing: {$missingCount}\n";

    if ($missingCount > 0) {
        echo "\n❌ Some settings are missing from system_settings table!\n";
        echo "Please run the migration script first.\n";
        exit;
    }

    // 4. Drop the settings table
    echo "\n4. Dropping settings table...\n";
    Schema::dropIfExists('settings');
    echo "✅ Settings table dropped successfully!\n";

    // 5. Verify table is dropped
    echo "\n5. Verifying table is dropped...\n";
    if (Schema::hasTable('settings')) {
        echo "❌ Settings table still exists\n";
    } else {
        echo "✅ Settings table successfully dropped\n";
    }

    // 6. Final verification
    echo "\n6. Final verification...\n";
    echo "- system_settings table exists: " . (Schema::hasTable('system_settings') ? 'Yes' : 'No') . "\n";
    echo "- system_settings record count: " . SystemSetting::count() . "\n";

    echo "\n✅ Settings table dropped successfully!\n";
    echo "All data is now in system_settings table.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
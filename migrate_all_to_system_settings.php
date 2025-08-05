<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use App\Models\SystemSetting;

try {
    echo "=== Migrating All Settings to SystemSettings ===\n\n";

    // 1. Get all settings from old table
    echo "1. Reading all settings from old table...\n";
    $oldSettings = Setting::all();
    echo "Found " . $oldSettings->count() . " settings in old table\n\n";

    // 2. Migrate each setting
    echo "2. Migrating settings to new table...\n";
    $migratedCount = 0;
    $skippedCount = 0;

    foreach ($oldSettings as $setting) {
        // Check if setting already exists in new table
        $existingSetting = SystemSetting::where('key', $setting->key)->first();
        
        if ($existingSetting) {
            echo "- Skipped (already exists): {$setting->key}\n";
            $skippedCount++;
        } else {
            // Create new setting
            SystemSetting::create([
                'key' => $setting->key,
                'value' => $setting->value
            ]);
            echo "- Migrated: {$setting->key}\n";
            $migratedCount++;
        }
    }

    echo "\nMigration Summary:\n";
    echo "- Total settings in old table: " . $oldSettings->count() . "\n";
    echo "- Migrated: {$migratedCount}\n";
    echo "- Skipped (already exists): {$skippedCount}\n\n";

    // 3. Verify migration
    echo "3. Verifying migration...\n";
    $newSettingsCount = SystemSetting::count();
    echo "Total settings in new table: {$newSettingsCount}\n";

    // 4. List all templates in new table
    echo "\n4. All templates in system_settings:\n";
    $templates = SystemSetting::where('key', 'like', '%template%')->get();
    foreach ($templates as $template) {
        echo "- {$template->key}: {$template->value}\n";
    }

    // 5. Check specific template
    echo "\n5. Checking owner_welcome_sms template:\n";
    $welcomeTemplate = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    if ($welcomeTemplate) {
        $data = json_decode($welcomeTemplate->value, true);
        echo "- Content: " . ($data['content'] ?? 'N/A') . "\n";
    } else {
        echo "- Template not found!\n";
    }

    echo "\n✅ Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
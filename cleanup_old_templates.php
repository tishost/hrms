<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use App\Models\SystemSetting;

try {
    echo "=== Cleaning up old templates ===\n\n";

    // Check templates in old settings table
    echo "1. Checking old settings table:\n";
    $oldTemplates = Setting::where('key', 'like', '%template%')->get();
    foreach ($oldTemplates as $template) {
        echo "- {$template->key}: {$template->value}\n";
    }

    // Check templates in new system_settings table
    echo "\n2. Checking new system_settings table:\n";
    $newTemplates = SystemSetting::where('key', 'like', '%template%')->get();
    foreach ($newTemplates as $template) {
        echo "- {$template->key}: {$template->value}\n";
    }

    // Delete old templates
    echo "\n3. Deleting old templates:\n";
    $deletedCount = Setting::where('key', 'like', '%template%')->delete();
    echo "Deleted {$deletedCount} old templates\n";

    // Verify deletion
    echo "\n4. Verifying old templates are deleted:\n";
    $remainingOldTemplates = Setting::where('key', 'like', '%template%')->get();
    if ($remainingOldTemplates->count() === 0) {
        echo "✅ All old templates deleted successfully\n";
    } else {
        echo "❌ Some old templates still exist:\n";
        foreach ($remainingOldTemplates as $template) {
            echo "- {$template->key}\n";
        }
    }

    echo "\n✅ Cleanup completed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
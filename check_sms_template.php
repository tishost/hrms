<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

try {
    echo "=== Checking SMS Templates ===\n\n";

    // Check if owner welcome SMS template exists
    $template = Setting::where('key', 'template_owner_welcome_sms')->first();
    
    if (!$template) {
        echo "❌ Owner welcome SMS template not found!\n";
        echo "Creating template...\n";
        
        Setting::create([
            'key' => 'template_owner_welcome_sms',
            'value' => json_encode([
                'subject' => 'Welcome to HRMS',
                'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
            ])
        ]);
        
        echo "✅ Created owner_welcome_sms template\n";
    } else {
        echo "✅ Owner welcome SMS template exists\n";
        echo "Content: " . $template->value . "\n";
    }

    // List all SMS templates
    echo "\n=== All SMS Templates ===\n";
    $templates = Setting::where('key', 'like', '%template%')->where('key', 'like', '%sms%')->get();
    foreach ($templates as $template) {
        echo "- {$template->key}\n";
    }

    echo "\n✅ SMS template check completed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
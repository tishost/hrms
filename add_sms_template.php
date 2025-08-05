<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;

try {
    // Add owner welcome SMS template
    Setting::updateOrCreate(
        ['key' => 'template_owner_welcome_sms'],
        [
            'value' => json_encode([
                'subject' => 'Welcome to HRMS',
                'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
            ])
        ]
    );

    echo "Owner welcome SMS template created successfully!\n";

    // List all SMS templates
    $templates = Setting::where('key', 'like', '%sms%')->get();
    echo "\nAll SMS templates:\n";
    foreach ($templates as $template) {
        echo "Key: " . $template->key . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
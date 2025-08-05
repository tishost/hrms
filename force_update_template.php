<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;

try {
    echo "=== Force Updating Template ===\n\n";

    // Delete existing template
    SystemSetting::where('key', 'template_owner_welcome_sms')->delete();
    echo "Deleted existing template\n";

    // Create new template with correct format
    SystemSetting::create([
        'key' => 'template_owner_welcome_sms',
        'value' => json_encode([
            'subject' => 'Welcome to HRMS',
            'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
        ])
    ]);
    
    echo "Created new template with {{name}} format\n\n";

    // Verify the template
    $template = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    if ($template) {
        $data = json_decode($template->value, true);
        echo "Verified template content: " . $data['content'] . "\n";
    }

    echo "✅ Template updated successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
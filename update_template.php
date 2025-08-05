<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;

try {
    echo "=== Updating Template Format ===\n\n";

    // Update the owner welcome SMS template
    $templateData = [
        'subject' => 'Welcome to HRMS',
        'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
    ];
    
    SystemSetting::updateOrCreate(
        ['key' => 'template_owner_welcome_sms'],
        ['value' => json_encode($templateData)]
    );
    
    echo "✅ Template updated successfully!\n";
    echo "New content: " . $templateData['content'] . "\n\n";

    // Test the replacement
    $testMessage = $templateData['content'];
    $variables = ['name' => 'Samiul'];
    
    $replacedMessage = $testMessage;
    foreach ($variables as $key => $value) {
        $replacedMessage = str_replace('{{' . $key . '}}', $value, $replacedMessage);
    }
    
    echo "Test replacement:\n";
    echo "- Original: " . $testMessage . "\n";
    echo "- Replaced: " . $replacedMessage . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
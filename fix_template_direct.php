<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;

try {
    echo "=== Fixing Template Directly ===\n\n";

    // Update the template with correct format
    $templateData = [
        'subject' => 'Welcome to HRMS',
        'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
    ];
    
    $result = SystemSetting::updateOrCreate(
        ['key' => 'template_owner_welcome_sms'],
        ['value' => json_encode($templateData)]
    );
    
    echo "✅ Template updated successfully!\n";
    echo "New content: " . $templateData['content'] . "\n\n";

    // Verify the update
    $template = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    if ($template) {
        $data = json_decode($template->value, true);
        echo "✅ Verified template content: " . $data['content'] . "\n\n";
    }

    // Test variable replacement
    $testMessage = $templateData['content'];
    $variables = ['name' => 'Samiul'];
    
    $replacedMessage = $testMessage;
    foreach ($variables as $key => $value) {
        $replacedMessage = str_replace('{{' . $key . '}}', $value, $replacedMessage);
    }
    
    echo "Test replacement:\n";
    echo "- Original: " . $testMessage . "\n";
    echo "- Variables: " . json_encode($variables) . "\n";
    echo "- Replaced: " . $replacedMessage . "\n\n";

    echo "✅ Template fix completed!\n";
    echo "Now owner registration SMS will show the correct name.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
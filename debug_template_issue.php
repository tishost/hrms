<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;
use App\Services\NotificationService;

try {
    echo "=== Debugging Template Variable Issue ===\n\n";

    // 1. Check template content
    echo "1. Template content from database:\n";
    $template = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    if ($template) {
        $data = json_decode($template->value, true);
        echo "- Content: " . $data['content'] . "\n\n";
    }

    // 2. Test manual variable replacement
    echo "2. Testing manual variable replacement:\n";
    $testMessage = "Welcome {{name}} to HRMS! Your account has been successfully created.";
    $variables = ['name' => 'Samiul'];
    
    $replacedMessage = $testMessage;
    foreach ($variables as $key => $value) {
        $replacedMessage = str_replace('{{' . $key . '}}', $value, $replacedMessage);
    }
    
    echo "- Original: " . $testMessage . "\n";
    echo "- Variables: " . json_encode($variables) . "\n";
    echo "- Replaced: " . $replacedMessage . "\n\n";

    // 3. Test NotificationService
    echo "3. Testing NotificationService:\n";
    $notificationService = new NotificationService();
    
    // Test direct SMS with variables
    $result = $notificationService->sendSms('01718262530', $testMessage, null, $variables);
    echo "- Direct SMS result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

    // 4. Test template notification
    echo "4. Testing template notification:\n";
    $templateResult = $notificationService->sendTemplateNotification('sms', '01718262530', 'owner_welcome_sms', $variables);
    echo "- Template result: " . json_encode($templateResult, JSON_PRETTY_PRINT) . "\n\n";

    // 5. Check what template is actually being used
    echo "5. Checking actual template being used:\n";
    $actualTemplate = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    if ($actualTemplate) {
        $actualData = json_decode($actualTemplate->value, true);
        echo "- Actual template content: " . $actualData['content'] . "\n";
        
        // Test replacement on actual template
        $actualReplaced = $actualData['content'];
        foreach ($variables as $key => $value) {
            $actualReplaced = str_replace('{{' . $key . '}}', $value, $actualReplaced);
        }
        echo "- Actual template replaced: " . $actualReplaced . "\n\n";
    }

    echo "✅ Debug completed!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
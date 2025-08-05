<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;

try {
    echo "=== Fixing Template Variables ===\n\n";

    // 1. Check current template
    echo "1. Current template content:\n";
    $template = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    if ($template) {
        $data = json_decode($template->value, true);
        echo "- Current content: " . $data['content'] . "\n\n";
    }

    // 2. Update template to use {{name}} format
    echo "2. Updating template to use {{name}} format:\n";
    $newTemplateData = [
        'subject' => 'Welcome to HRMS',
        'content' => 'Welcome {{name}} to HRMS! Your account has been successfully created. You can now manage your properties and tenants efficiently.'
    ];
    
    SystemSetting::updateOrCreate(
        ['key' => 'template_owner_welcome_sms'],
        ['value' => json_encode($newTemplateData)]
    );
    
    echo "- Updated template content: " . $newTemplateData['content'] . "\n\n";

    // 3. Test variable replacement
    echo "3. Testing variable replacement:\n";
    $testMessage = $newTemplateData['content'];
    $variables = ['name' => 'Samiul'];
    
    // Manual replacement test
    $replacedMessage = $testMessage;
    foreach ($variables as $key => $value) {
        $replacedMessage = str_replace('{{' . $key . '}}', $value, $replacedMessage);
    }
    
    echo "- Original: " . $testMessage . "\n";
    echo "- Variables: " . json_encode($variables) . "\n";
    echo "- Replaced: " . $replacedMessage . "\n\n";

    // 4. Update other templates too
    echo "4. Updating other templates:\n";
    $templates = [
        'template_welcome_sms' => 'Welcome {{name}} to HRMS! Your account has been successfully created.',
        'template_otp_verification_sms' => 'Your OTP is {{otp}}. Please use this code to verify your account.',
        'template_payment_confirmation_sms' => 'Payment of ${{amount}} received. Invoice: {{invoice_number}}. Thank you!',
        'template_invoice_reminder_sms' => 'Invoice {{invoice_number}} for ${{amount}} is due on {{due_date}}. Please pay on time.'
    ];
    
    foreach ($templates as $key => $content) {
        $templateData = [
            'subject' => 'HRMS Notification',
            'content' => $content
        ];
        
        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($templateData)]
        );
        
        echo "- Updated: $key\n";
    }

    echo "\n✅ Template variables fixed!\n";
    echo "Now templates will use {{variable}} format and variables will be replaced correctly.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 
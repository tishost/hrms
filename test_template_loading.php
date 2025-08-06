<?php

require_once 'vendor/autoload.php';

use App\Models\SystemSetting;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing template loading functionality...\n\n";

// Test template names
$testTemplates = [
    'password_reset_otp_sms',
    'password_reset_email',
    'otp_verification_sms'
];

foreach ($testTemplates as $templateName) {
    echo "📖 Testing template: {$templateName}\n";
    
    // Check if template exists in database
    $template = SystemSetting::where('key', 'template_' . $templateName)->first();
    
    if ($template) {
        echo "✅ Template found in database\n";
        echo "📄 Raw value: " . $template->value . "\n";
        
        $data = json_decode($template->value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ JSON Valid\n";
            if (is_array($data)) {
                foreach ($data as $field => $value) {
                    echo "   {$field}: " . (empty($value) ? 'EMPTY' : substr($value, 0, 50) . '...') . "\n";
                }
            }
        } else {
            echo "❌ JSON Error: " . json_last_error_msg() . "\n";
        }
    } else {
        echo "❌ Template not found in database\n";
        
        // Create a test template
        $testContent = 'Test content for ' . $templateName;
        $templateData = [
            'content' => $testContent
        ];
        
        SystemSetting::setValue('template_' . $templateName, json_encode($templateData));
        echo "✅ Created test template\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Test API endpoint simulation
echo "🌐 Testing API endpoint simulation...\n";

// Simulate the getTemplate method
$request = new \Illuminate\Http\Request();
$request->merge(['template' => 'password_reset_otp_sms']);

try {
    $controller = new \App\Http\Controllers\Admin\NotificationSettingsController();
    $response = $controller->getTemplate($request);
    
    echo "✅ API endpoint working\n";
    echo "📄 Response: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "❌ API endpoint error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test completed!\n";

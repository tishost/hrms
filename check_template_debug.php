<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SystemSetting;

try {
    echo "=== Template Debug ===\n\n";

    // Check template content
    $template = SystemSetting::where('key', 'template_owner_welcome_sms')->first();
    
    if ($template) {
        echo "Template found:\n";
        echo "Key: " . $template->key . "\n";
        echo "Value: " . $template->value . "\n\n";
        
        $data = json_decode($template->value, true);
        echo "Decoded data:\n";
        echo "Subject: " . ($data['subject'] ?? 'N/A') . "\n";
        echo "Content: " . ($data['content'] ?? 'N/A') . "\n\n";
        
        // Test variable replacement
        $content = $data['content'] ?? '';
        $variables = ['name' => 'Samiul'];
        
        echo "Testing variable replacement:\n";
        echo "Original content: " . $content . "\n";
        echo "Variables: " . json_encode($variables) . "\n";
        
        // Manual replacement
        $replaced = $content;
        foreach ($variables as $key => $value) {
            $replaced = str_replace('{{' . $key . '}}', $value, $replaced);
            $replaced = str_replace('{' . $key . '}', $value, $replaced);
        }
        
        echo "Replaced content: " . $replaced . "\n\n";
        
        // Check if there are any other templates with similar names
        echo "Checking for similar templates:\n";
        $similarTemplates = SystemSetting::where('key', 'like', '%owner_welcome%')->get();
        foreach ($similarTemplates as $t) {
            echo "- " . $t->key . ": " . $t->value . "\n";
        }
        
    } else {
        echo "Template not found!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 
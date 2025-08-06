<?php

require_once 'vendor/autoload.php';

use App\Models\SystemSetting;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Comprehensive Template System Test\n";
echo "=====================================\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    $testSetting = SystemSetting::first();
    echo "✅ Database connection working\n";
} catch (\Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Template Save
echo "\n2. Testing Template Save...\n";
$testTemplateName = 'test_template_sms';
$testContent = 'Test SMS content with {otp} variable';

try {
    $templateData = ['content' => $testContent];
    $result = SystemSetting::setValue('template_' . $testTemplateName, json_encode($templateData));
    echo "✅ Template save working\n";
} catch (\Exception $e) {
    echo "❌ Template save error: " . $e->getMessage() . "\n";
}

// Test 3: Template Load
echo "\n3. Testing Template Load...\n";
try {
    $template = SystemSetting::where('key', 'template_' . $testTemplateName)->first();
    if ($template) {
        echo "✅ Template load working\n";
        echo "📄 Content: " . $template->value . "\n";
    } else {
        echo "❌ Template not found\n";
    }
} catch (\Exception $e) {
    echo "❌ Template load error: " . $e->getMessage() . "\n";
}

// Test 4: JSON Validation
echo "\n4. Testing JSON Validation...\n";
try {
    $data = json_decode($template->value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ JSON validation working\n";
        echo "📝 Decoded content: " . ($data['content'] ?? 'NULL') . "\n";
    } else {
        echo "❌ JSON error: " . json_last_error_msg() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ JSON validation error: " . $e->getMessage() . "\n";
}

// Test 5: Authentication Check
echo "\n5. Testing Authentication...\n";
try {
    $user = User::first();
    if ($user) {
        echo "✅ User found: " . $user->email . "\n";
        echo "🔑 Has roles: " . ($user->hasRole('super_admin') ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ No users found\n";
    }
} catch (\Exception $e) {
    echo "❌ Authentication error: " . $e->getMessage() . "\n";
}

// Test 6: Route Check
echo "\n6. Testing Routes...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = collect($routes)->filter(function ($route) {
        return str_contains($route->uri, 'admin');
    });
    echo "✅ Found " . $adminRoutes->count() . " admin routes\n";
} catch (\Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
}

// Test 7: Existing Templates
echo "\n7. Checking Existing Templates...\n";
$existingTemplates = SystemSetting::where('key', 'like', 'template_%')->get();
echo "📋 Found " . $existingTemplates->count() . " templates:\n";
foreach ($existingTemplates as $template) {
    echo "   - " . $template->key . "\n";
}

// Clean up test template
SystemSetting::where('key', 'template_' . $testTemplateName)->delete();

echo "\n🎉 Comprehensive test completed!\n";

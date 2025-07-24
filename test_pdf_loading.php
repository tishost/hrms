<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PDF Loading Performance Test ===\n\n";

// Test with owner user
$ownerEmail = 'owner@hrms.com';
$ownerUser = \App\Models\User::where('email', $ownerEmail)->first();

if ($ownerUser) {
    echo "Testing with Owner: {$ownerUser->email}\n";

    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function () use ($ownerUser) {
        return $ownerUser;
    });

    $ownerController = new \App\Http\Controllers\Api\OwnerController();

    // Test PDF generation time
    $startTime = microtime(true);
    $response = $ownerController->downloadInvoicePDF($request, 2);
    $endTime = microtime(true);

    $generationTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

    echo "PDF Generation Time: " . number_format($generationTime, 2) . " ms\n";
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Content Length: " . strlen($response->getContent()) . " bytes\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n";

    // Check if PDF is valid
    $pdfContent = $response->getContent();
    if (strpos($pdfContent, '%PDF-') === 0) {
        echo "✅ PDF is valid (starts with %PDF-)\n";
    } else {
        echo "❌ PDF is not valid\n";
        echo "First 50 chars: " . substr($pdfContent, 0, 50) . "\n";
    }

    // Check headers
    echo "\nResponse Headers:\n";
    foreach ($response->headers->all() as $name => $values) {
        echo "- $name: " . implode(', ', $values) . "\n";
    }

} else {
    echo "❌ Owner user not found\n";
}

echo "\n=== Mobile App Loading Issues ===\n";
echo "Common causes of stuck loading:\n";
echo "1. Network timeout\n";
echo "2. Large PDF file\n";
echo "3. WebView configuration\n";
echo "4. API response headers\n";
echo "5. Cache issues\n\n";

echo "Solutions applied:\n";
echo "✅ Reduced timeout to 30 seconds\n";
echo "✅ Added proper cache headers\n";
echo "✅ Added retry and reload buttons\n";
echo "✅ Enhanced error handling\n";
echo "✅ Added debug logging\n\n";

echo "=== Test Complete ===\n";
?>

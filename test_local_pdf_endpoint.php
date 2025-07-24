<?php
// Test local PDF endpoint for emulator
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\TenantController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LOCAL PDF ENDPOINT TEST ===\n";
echo "Testing PDF endpoints for emulator...\n\n";

// Test 1: Check if we can access the local server
$localUrl = 'http://localhost/hrms/public/api';
echo "1. Testing local server access...\n";
echo "   URL: $localUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $localUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "   ✅ Local server is accessible\n";
} else {
    echo "   ❌ Local server not accessible (HTTP $httpCode)\n";
    echo "   Please make sure WAMP server is running\n";
}

// Test 2: Check if we have invoices in database
echo "\n2. Checking database for invoices...\n";
try {
    $invoices = \App\Models\Invoice::with(['tenant', 'unit', 'property'])->limit(5)->get();
    echo "   Found " . $invoices->count() . " invoices\n";

    if ($invoices->count() > 0) {
        $firstInvoice = $invoices->first();
        echo "   First Invoice ID: " . $firstInvoice->id . "\n";
        echo "   Invoice Number: " . $firstInvoice->invoice_number . "\n";
        echo "   Owner ID: " . $firstInvoice->owner_id . "\n";
        echo "   Tenant ID: " . $firstInvoice->tenant_id . "\n";
    } else {
        echo "   ❌ No invoices found in database\n";
        echo "   Please create some invoices first\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Test Owner PDF endpoint
echo "\n3. Testing Owner PDF endpoint...\n";
if (isset($firstInvoice)) {
    $ownerPdfUrl = "http://localhost/hrms/public/api/owner/invoices/{$firstInvoice->id}/pdf-file";
    echo "   URL: $ownerPdfUrl\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ownerPdfUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "   ✅ Owner PDF endpoint working\n";
    } elseif ($httpCode == 401) {
        echo "   ⚠️ Owner PDF endpoint requires authentication\n";
    } elseif ($httpCode == 404) {
        echo "   ❌ Owner PDF endpoint not found\n";
    } else {
        echo "   ❌ Owner PDF endpoint error (HTTP $httpCode)\n";
    }
}

// Test 4: Test Tenant PDF endpoint
echo "\n4. Testing Tenant PDF endpoint...\n";
if (isset($firstInvoice)) {
    $tenantPdfUrl = "http://localhost/hrms/public/api/tenant/invoices/{$firstInvoice->id}/pdf-file";
    echo "   URL: $tenantPdfUrl\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tenantPdfUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "   ✅ Tenant PDF endpoint working\n";
    } elseif ($httpCode == 401) {
        echo "   ⚠️ Tenant PDF endpoint requires authentication\n";
    } elseif ($httpCode == 404) {
        echo "   ❌ Tenant PDF endpoint not found\n";
    } else {
        echo "   ❌ Tenant PDF endpoint error (HTTP $httpCode)\n";
    }
}

// Test 5: Emulator specific URL test
echo "\n5. Testing emulator URL (10.0.2.2)...\n";
$emulatorUrl = "http://10.0.2.2/hrms/public/api";
echo "   URL: $emulatorUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $emulatorUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "   ✅ Emulator URL accessible\n";
} else {
    echo "   ❌ Emulator URL not accessible (HTTP $httpCode)\n";
    echo "   This is normal - 10.0.2.2 only works from emulator\n";
}

echo "\n=== SUMMARY ===\n";
echo "For emulator testing:\n";
echo "1. Make sure WAMP server is running\n";
echo "2. Use URL: http://10.0.2.2/hrms/public/api\n";
echo "3. Check if invoices exist in database\n";
echo "4. Test with authentication token\n";
echo "================\n";
?>

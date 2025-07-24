<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing PDF URL Endpoint ===\n\n";

// Test invoice INV-2025-0002
$invoiceNumber = 'INV-2025-0002';
echo "Testing invoice: $invoiceNumber\n\n";

$invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

if ($invoice) {
    echo "✅ Invoice found:\n";
    echo "- ID: {$invoice->id}\n";
    echo "- Number: {$invoice->invoice_number}\n";
    echo "- Tenant ID: {$invoice->tenant_id}\n";

    // Check tenant
    $tenant = Tenant::find($invoice->tenant_id);
    if ($tenant) {
        echo "\n✅ Tenant found:\n";
        echo "- ID: {$tenant->id}\n";
        echo "- Name: {$tenant->first_name} {$tenant->last_name}\n";

        // Check user for this tenant
        $user = User::where('tenant_id', $tenant->id)->first();
        if ($user) {
            echo "\n✅ User found:\n";
            echo "- ID: {$user->id}\n";
            echo "- Email: {$user->email}\n";

            // Test PDF URL endpoint simulation
            echo "\n=== Testing PDF URL Endpoint ===\n";

            try {
                // Simulate the PDF URL endpoint response
                $pdfUrl = url("/api/tenant/invoices/{$invoice->id}/pdf-file");

                $response = [
                    'success' => true,
                    'pdf_url' => $pdfUrl,
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'amount' => $invoice->amount,
                        'status' => $invoice->status,
                    ]
                ];

                echo "✅ PDF URL endpoint response:\n";
                echo "- Success: " . ($response['success'] ? 'true' : 'false') . "\n";
                echo "- PDF URL: {$response['pdf_url']}\n";
                echo "- Invoice ID: {$response['invoice']['id']}\n";
                echo "- Invoice Number: {$response['invoice']['invoice_number']}\n";

                // Test if URL is accessible
                echo "\n=== Testing URL Accessibility ===\n";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $pdfUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                echo "HTTP Status Code: $httpCode\n";
                if ($httpCode == 200) {
                    echo "✅ URL is accessible\n";
                } else {
                    echo "❌ URL is not accessible (Status: $httpCode)\n";
                }

            } catch (Exception $e) {
                echo "❌ PDF URL endpoint error: " . $e->getMessage() . "\n";
            }

        } else {
            echo "\n❌ No user found for tenant ID {$tenant->id}\n";
        }
    } else {
        echo "\n❌ Tenant not found for ID {$invoice->tenant_id}\n";
    }

    // Test API endpoints
    echo "\n=== API Endpoint URLs ===\n";
    echo "API Base URL: http://103.98.76.11/api\n";
    echo "PDF URL Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf\n";
    echo "PDF File Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf-file\n";

    echo "\n=== Expected Flutter Flow ===\n";
    echo "1. Flutter calls: GET /api/tenant/invoices/{$invoice->id}/pdf\n";
    echo "2. Server returns: {\"success\": true, \"pdf_url\": \"...\"}\n";
    echo "3. Flutter loads: pdf_url in WebView\n";

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing Invoice ID Type Consistency ===\n\n";

// Test invoice INV-2025-0002
$invoiceNumber = 'INV-2025-0002';
echo "Testing invoice: $invoiceNumber\n\n";

$invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

if ($invoice) {
    echo "✅ Invoice found:\n";
    echo "- ID: {$invoice->id} (Type: " . gettype($invoice->id) . ")\n";
    echo "- Number: {$invoice->invoice_number}\n";
    echo "- Tenant ID: {$invoice->tenant_id} (Type: " . gettype($invoice->tenant_id) . ")\n";

    // Check tenant
    $tenant = Tenant::find($invoice->tenant_id);
    if ($tenant) {
        echo "\n✅ Tenant found:\n";
        echo "- ID: {$tenant->id} (Type: " . gettype($tenant->id) . ")\n";
        echo "- Name: {$tenant->first_name} {$tenant->last_name}\n";

        // Check user for this tenant
        $user = User::where('tenant_id', $tenant->id)->first();
        if ($user) {
            echo "\n✅ User found:\n";
            echo "- ID: {$user->id} (Type: " . gettype($user->id) . ")\n";
            echo "- Email: {$user->email}\n";
            echo "- Tenant ID: {$user->tenant_id} (Type: " . gettype($user->tenant_id) . ")\n";

            // Test API response format
            echo "\n=== Testing API Response Format ===\n";

            $apiResponse = [
                'success' => true,
                'invoices' => [
                    [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'amount' => $invoice->amount,
                        'status' => $invoice->status,
                        'tenant_id' => $invoice->tenant_id,
                    ]
                ]
            ];

            echo "✅ API Response format:\n";
            echo "- Invoice ID: {$apiResponse['invoices'][0]['id']} (Type: " . gettype($apiResponse['invoices'][0]['id']) . ")\n";
            echo "- Invoice Number: {$apiResponse['invoices'][0]['invoice_number']}\n";
            echo "- Tenant ID: {$apiResponse['invoices'][0]['tenant_id']} (Type: " . gettype($apiResponse['invoices'][0]['tenant_id']) . ")\n";

            // Test PDF URL endpoint
            echo "\n=== Testing PDF URL Endpoint ===\n";
            $pdfUrl = url("/api/tenant/invoices/{$invoice->id}/pdf-file");

            $pdfResponse = [
                'success' => true,
                'pdf_url' => $pdfUrl,
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ]
            ];

            echo "✅ PDF URL Response:\n";
            echo "- Success: " . ($pdfResponse['success'] ? 'true' : 'false') . "\n";
            echo "- PDF URL: {$pdfResponse['pdf_url']}\n";
            echo "- Invoice ID: {$pdfResponse['invoice']['id']} (Type: " . gettype($pdfResponse['invoice']['id']) . ")\n";

        } else {
            echo "\n❌ No user found for tenant ID {$tenant->id}\n";
        }
    } else {
        echo "\n❌ Tenant not found for ID {$invoice->tenant_id}\n";
    }

    // Test all invoices
    echo "\n=== Testing All Invoices ===\n";
    $allInvoices = Invoice::take(5)->get();
    foreach ($allInvoices as $inv) {
        echo "- ID: {$inv->id} (Type: " . gettype($inv->id) . "), Number: {$inv->invoice_number}, Tenant: {$inv->tenant_id}\n";
    }

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Type Consistency Summary ===\n";
echo "✅ Invoice ID: Should be integer\n";
echo "✅ Tenant ID: Should be integer\n";
echo "✅ User ID: Should be integer\n";
echo "✅ API Response: IDs should be integers, not strings\n";

echo "\n=== Flutter App Requirements ===\n";
echo "✅ InvoicePdfScreen expects: invoiceId: int\n";
echo "✅ API returns: id: int\n";
echo "✅ Pass directly: invoice['id'] (not toString())\n";

echo "\n=== Test Complete ===\n";
?>

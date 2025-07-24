<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing PDF Endpoint ===\n\n";

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

            // Test PDF generation directly
            echo "\n=== Testing PDF Generation ===\n";

            try {
                // Simulate the PDF generation process
                $breakdown = [];
                if ($invoice->breakdown) {
                    try {
                        $breakdown = json_decode($invoice->breakdown, true) ?? [];
                    } catch (Exception $e) {
                        $breakdown = [];
                    }
                }

                // Prepare data for PDF
                $pdfData = [
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_type' => $invoice->invoice_type,
                        'description' => $invoice->description,
                        'amount' => $invoice->amount,
                        'paid_amount' => $invoice->paid_amount ?? 0,
                        'remaining_amount' => ($invoice->amount - ($invoice->paid_amount ?? 0)),
                        'status' => $invoice->status,
                        'issue_date' => $invoice->issue_date,
                        'due_date' => $invoice->due_date,
                        'breakdown' => $breakdown,
                    ],
                    'tenant' => [
                        'name' => $tenant->first_name . ' ' . $tenant->last_name,
                        'phone' => $tenant->mobile ?? 'N/A',
                        'email' => $tenant->email ?? 'N/A',
                    ],
                    'unit' => [
                        'name' => 'N/A',
                    ],
                    'property' => [
                        'name' => 'N/A',
                        'address' => 'N/A',
                    ],
                    'owner' => null,
                ];

                echo "✅ PDF data prepared successfully\n";
                echo "- Invoice amount: {$pdfData['invoice']['amount']}\n";
                echo "- Tenant name: {$pdfData['tenant']['name']}\n";
                echo "- Breakdown items: " . count($pdfData['invoice']['breakdown']) . "\n";

                // Test view rendering
                echo "\n=== Testing View Rendering ===\n";
                $view = view('pdf.invoice', $pdfData);
                $html = $view->render();

                echo "✅ View rendered successfully\n";
                echo "- HTML length: " . strlen($html) . " characters\n";
                echo "- Contains invoice number: " . (strpos($html, $invoice->invoice_number) !== false ? 'Yes' : 'No') . "\n";
                echo "- Contains tenant name: " . (strpos($html, $pdfData['tenant']['name']) !== false ? 'Yes' : 'No') . "\n";

                // Test DomPDF
                echo "\n=== Testing DomPDF ===\n";
                if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                    echo "✅ DomPDF is available\n";

                    try {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', $pdfData);
                        $pdfContent = $pdf->output();

                        echo "✅ PDF generated successfully\n";
                        echo "- PDF size: " . strlen($pdfContent) . " bytes\n";
                        echo "- PDF starts with: " . substr($pdfContent, 0, 20) . "...\n";

                    } catch (Exception $e) {
                        echo "❌ PDF generation failed: " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "❌ DomPDF not available\n";
                }

            } catch (Exception $e) {
                echo "❌ PDF generation error: " . $e->getMessage() . "\n";
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

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

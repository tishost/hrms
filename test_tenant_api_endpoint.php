<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing Tenant API Endpoint ===\n\n";

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

            // Test API endpoint simulation
            echo "\n=== API Endpoint Test ===\n";

            try {
                // Simulate tenant API controller logic
                echo "1. Get invoice with tenant permission:\n";
                $tenantInvoice = Invoice::where('id', $invoice->id)
                    ->where('tenant_id', $tenant->id)
                    ->with(['tenant', 'unit', 'property'])
                    ->first();

                if ($tenantInvoice) {
                    echo "✅ Invoice found for tenant\n";
                    echo "- Invoice ID: {$tenantInvoice->id}\n";
                    echo "- Invoice Number: {$tenantInvoice->invoice_number}\n";
                    echo "- Amount: {$tenantInvoice->amount}\n";

                    // Test PDF generation with proper headers
                    echo "\n2. Test PDF generation with proper headers:\n";

                    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                        echo "✅ DomPDF is available\n";

                        try {
                            // Use owner template with proper headers
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.invoices.pdf', compact('invoice'));
                            $pdfContent = $pdf->output();

                            echo "✅ PDF generated successfully\n";
                            echo "- PDF size: " . strlen($pdfContent) . " bytes\n";
                            echo "- PDF starts with: " . substr($pdfContent, 0, 50) . "...\n";

                            // Simulate response headers
                            echo "\n3. Response headers:\n";
                            $headers = [
                                'Content-Type' => 'application/pdf',
                                'Content-Disposition' => 'inline; filename="invoice-' . $tenantInvoice->invoice_number . '.pdf"',
                                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                                'Pragma' => 'no-cache',
                                'Expires' => '0',
                            ];

                            foreach ($headers as $key => $value) {
                                echo "- $key: $value\n";
                            }

                            // Check if PDF contains expected content
                            if (strpos($pdfContent, 'INV-2025-0002') !== false) {
                                echo "✅ PDF contains invoice number\n";
                            }
                            if (strpos($pdfContent, '7400.00') !== false) {
                                echo "✅ PDF contains amount\n";
                            }

                        } catch (Exception $e) {
                            echo "❌ PDF generation failed: " . $e->getMessage() . "\n";
                        }
                    } else {
                        echo "❌ DomPDF not available\n";
                    }

                } else {
                    echo "❌ Invoice not found for tenant\n";
                }

            } catch (Exception $e) {
                echo "❌ API endpoint error: " . $e->getMessage() . "\n";
            }

            // API endpoint URLs
            echo "\n=== API Endpoint URLs ===\n";
            echo "API Base URL: http://103.98.76.11/api\n";
            echo "PDF Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf-file\n";
            echo "Expected Response: PDF file with proper headers\n";

        } else {
            echo "\n❌ No user found for tenant ID {$tenant->id}\n";
        }
    } else {
        echo "\n❌ Tenant not found for ID {$invoice->tenant_id}\n";
    }

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

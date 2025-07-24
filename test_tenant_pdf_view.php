<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing Tenant PDF View ===\n\n";

// Test invoice INV-2025-0002
$invoiceNumber = 'INV-2025-0002';
echo "Testing invoice: $invoiceNumber\n\n";

$invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

if ($invoice) {
    echo "✅ Invoice found:\n";
    echo "- ID: {$invoice->id}\n";
    echo "- Number: {$invoice->invoice_number}\n";
    echo "- Tenant ID: {$invoice->tenant_id}\n";
    echo "- Amount: {$invoice->amount}\n";
    echo "- Status: {$invoice->status}\n";

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

            // Test tenant PDF generation (exact same as API)
            echo "\n=== Testing Tenant PDF Generation ===\n";

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
                    echo "- Tenant: {$tenantInvoice->tenant->first_name} {$tenantInvoice->tenant->last_name}\n";
                    echo "- Unit: {$tenantInvoice->unit->name}\n";

                    // Test PDF generation with owner template
                    echo "\n2. Test PDF generation with owner template:\n";

                    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                        echo "✅ DomPDF is available\n";

                        try {
                            // Use owner template (same as tenant API)
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.invoices.pdf', compact('invoice'));
                            $pdfContent = $pdf->output();

                            echo "✅ PDF generated successfully with owner template\n";
                            echo "- PDF size: " . strlen($pdfContent) . " bytes\n";
                            echo "- PDF starts with: " . substr($pdfContent, 0, 50) . "...\n";

                            // Check if PDF contains expected content
                            if (strpos($pdfContent, 'INV-2025-0002') !== false) {
                                echo "✅ PDF contains invoice number\n";
                            }
                            if (strpos($pdfContent, '7400.00') !== false) {
                                echo "✅ PDF contains amount\n";
                            }

                        } catch (Exception $e) {
                            echo "❌ PDF generation failed: " . $e->getMessage() . "\n";
                            echo "Error details: " . $e->getTraceAsString() . "\n";
                        }
                    } else {
                        echo "❌ DomPDF not available\n";
                    }

                } else {
                    echo "❌ Invoice not found for tenant\n";
                }

            } catch (Exception $e) {
                echo "❌ Tenant PDF generation error: " . $e->getMessage() . "\n";
            }

            // Test API endpoint simulation
            echo "\n=== API Endpoint Test ===\n";
            echo "API URL: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf-file\n";
            echo "Expected: PDF file download\n";

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

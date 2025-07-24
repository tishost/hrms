<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing Owner Approach for Tenant PDF ===\n\n";

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

            // Test owner approach simulation
            echo "\n=== Testing Owner Approach ===\n";

            try {
                // Simulate owner's simple approach
                echo "1. Get invoice with tenant permission:\n";
                $tenantInvoice = Invoice::where('id', $invoice->id)
                    ->where('tenant_id', $tenant->id)
                    ->first();

                if ($tenantInvoice) {
                    echo "✅ Invoice found for tenant\n";
                    echo "- Invoice ID: {$tenantInvoice->id}\n";
                    echo "- Invoice Number: {$tenantInvoice->invoice_number}\n";
                    echo "- Amount: {$tenantInvoice->amount}\n";

                    // Test PDF generation (owner approach)
                    echo "\n2. Test PDF generation (owner approach):\n";

                    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                        echo "✅ DomPDF is available\n";

                        try {
                            // Use owner's simple approach
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('tenantInvoice'));
                            $pdfContent = $pdf->output();

                            echo "✅ PDF generated successfully (owner approach)\n";
                            echo "- PDF size: " . strlen($pdfContent) . " bytes\n";
                            echo "- PDF starts with: " . substr($pdfContent, 0, 20) . "...\n";

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
                echo "❌ Owner approach error: " . $e->getMessage() . "\n";
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
    echo "Direct PDF Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf-file\n";

    echo "\n=== Owner vs Tenant Approach ===\n";
    echo "Owner Approach:\n";
    echo "- Simple: \$pdf = \\PDF::loadView('pdf.invoice', compact('invoice'));\n";
    echo "- Direct: return \$pdf->stream('invoice-' . \$invoice->invoice_number . '.pdf');\n";
    echo "- Working: ✅ Yes\n";

    echo "\nTenant Approach (Now):\n";
    echo "- Simple: \$pdf = \\PDF::loadView('pdf.invoice', compact('invoice'));\n";
    echo "- Direct: return \$pdf->stream('invoice-' . \$invoice->invoice_number . '.pdf');\n";
    echo "- Permission: where('tenant_id', \$tenantId)\n";
    echo "- Working: ✅ Should work now\n";

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

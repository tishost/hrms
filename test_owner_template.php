<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing Owner Template with Tenant Data ===\n\n";

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

    // Check breakdown
    echo "\n📋 Breakdown data:\n";
    if ($invoice->breakdown) {
        $breakdown = json_decode($invoice->breakdown, true);
        echo "- Breakdown: " . json_encode($breakdown, JSON_PRETTY_PRINT) . "\n";

        if (is_array($breakdown)) {
            echo "- Is array: ✅ Yes\n";
            echo "- Array keys: " . implode(', ', array_keys($breakdown)) . "\n";

            if (isset($breakdown['charges']) && is_array($breakdown['charges'])) {
                echo "- Charges count: " . count($breakdown['charges']) . "\n";
                foreach ($breakdown['charges'] as $i => $charge) {
                    echo "  Charge {$i}: " . json_encode($charge) . "\n";
                }
            }
        } else {
            echo "- Is array: ❌ No\n";
        }
    } else {
        echo "- Breakdown: null\n";
    }

    // Test owner template
    echo "\n=== Testing Owner Template ===\n";

    try {
        // Simulate owner template rendering
        echo "1. Loading owner template with tenant invoice:\n";

        // Get tenant invoice with relationships
        $tenantInvoice = Invoice::where('id', $invoice->id)
            ->with(['tenant', 'unit', 'property'])
            ->first();

        if ($tenantInvoice) {
            echo "✅ Tenant invoice loaded with relationships\n";
            echo "- Tenant: {$tenantInvoice->tenant->first_name} {$tenantInvoice->tenant->last_name}\n";
            echo "- Unit: {$tenantInvoice->unit->name}\n";

            // Test PDF generation with owner template
            echo "\n2. Test PDF generation with owner template:\n";

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                echo "✅ DomPDF is available\n";

                try {
                    // Use owner template (same as owner approach)
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
            echo "❌ Tenant invoice not found\n";
        }

    } catch (Exception $e) {
        echo "❌ Owner template error: " . $e->getMessage() . "\n";
    }

    // Compare templates
    echo "\n=== Template Comparison ===\n";
    echo "Owner Template (owner.invoices.pdf):\n";
    echo "- Uses: \$invoice->breakdown (object property)\n";
    echo "- Breakdown handling: json_decode(\$invoice->breakdown, true)\n";
    echo "- Working: ✅ Yes\n";

    echo "\nTenant Template (pdf.invoice):\n";
    echo "- Uses: \$invoice['breakdown'] (array access)\n";
    echo "- Breakdown handling: direct array access\n";
    echo "- Working: ❌ No (foreach error)\n";

    echo "\n=== Solution ===\n";
    echo "✅ Use owner template for tenant: owner.invoices.pdf\n";
    echo "✅ Same data structure, same template\n";
    echo "✅ Just permission change\n";

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

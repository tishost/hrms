<?php
// Test tenant invoice data
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Tenant Invoice Test ===\n\n";

// Test 1: Check if there are any tenants
echo "Test 1: Check Tenants\n";
$tenants = Tenant::all();
echo "Total tenants: " . $tenants->count() . "\n";

if ($tenants->count() > 0) {
    $firstTenant = $tenants->first();
    echo "First tenant ID: " . $firstTenant->id . "\n";
    echo "First tenant name: " . $firstTenant->first_name . " " . $firstTenant->last_name . "\n";

    // Test 2: Check invoices for this tenant
    echo "\nTest 2: Check Invoices for Tenant ID " . $firstTenant->id . "\n";
    $invoices = Invoice::where('tenant_id', $firstTenant->id)->get();
    echo "Total invoices for this tenant: " . $invoices->count() . "\n";

    if ($invoices->count() > 0) {
        $firstInvoice = $invoices->first();
        echo "First invoice ID: " . $firstInvoice->id . "\n";
        echo "First invoice number: " . $firstInvoice->invoice_number . "\n";
        echo "First invoice amount: " . $firstInvoice->amount . "\n";
        echo "First invoice status: " . $firstInvoice->status . "\n";

        // Test 3: Check if invoice exists with specific ID
        echo "\nTest 3: Check Specific Invoice\n";
        $testInvoice = Invoice::where('id', $firstInvoice->id)
            ->where('tenant_id', $firstTenant->id)
            ->first();

        if ($testInvoice) {
            echo "✅ Invoice found: " . $testInvoice->invoice_number . "\n";
        } else {
            echo "❌ Invoice not found\n";
        }

        // Test 4: Check API endpoint simulation
        echo "\nTest 4: API Endpoint Simulation\n";
        echo "API URL: http://103.98.76.11/api/tenant/invoices/" . $firstInvoice->id . "/pdf-file\n";
        echo "Expected tenant_id: " . $firstTenant->id . "\n";
        echo "Expected invoice_id: " . $firstInvoice->id . "\n";

    } else {
        echo "❌ No invoices found for this tenant\n";

        // Test 5: Check all invoices
        echo "\nTest 5: Check All Invoices\n";
        $allInvoices = Invoice::all();
        echo "Total invoices in system: " . $allInvoices->count() . "\n";

        if ($allInvoices->count() > 0) {
            echo "Sample invoices:\n";
            foreach ($allInvoices->take(5) as $invoice) {
                echo "- ID: " . $invoice->id . ", Number: " . $invoice->invoice_number .
                     ", Tenant: " . $invoice->tenant_id . ", Amount: " . $invoice->amount . "\n";
            }
        }
    }
} else {
    echo "❌ No tenants found in the system\n";
}

echo "\n=== Test Complete ===\n";
echo "If you see 'Invoice not found' error, check:\n";
echo "1. Tenant ID in the request\n";
echo "2. Invoice ID in the request\n";
echo "3. Invoice belongs to the correct tenant\n";
echo "4. Authentication token is valid\n";
?>

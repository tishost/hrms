<?php
// Test API endpoints
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== API Endpoints Test ===\n\n";

// Test 1: Check if there are any tenants
echo "Test 1: Check Tenants\n";
$tenants = Tenant::all();
echo "Total tenants: " . $tenants->count() . "\n";

if ($tenants->count() > 0) {
    $firstTenant = $tenants->first();
    echo "First tenant ID: " . $firstTenant->id . "\n";
    echo "First tenant name: " . $firstTenant->first_name . " " . $firstTenant->last_name . "\n";

    // Test 2: Check if tenant has user account
    echo "\nTest 2: Check Tenant User Account\n";
    $user = User::where('tenant_id', $firstTenant->id)->first();
    if ($user) {
        echo "User found: " . $user->email . "\n";
        echo "User ID: " . $user->id . "\n";
        echo "User roles: " . $user->roles->pluck('name')->join(', ') . "\n";

        // Test 3: Check invoices for this tenant
        echo "\nTest 3: Check Invoices for Tenant ID " . $firstTenant->id . "\n";
        $invoices = Invoice::where('tenant_id', $firstTenant->id)->get();
        echo "Total invoices for this tenant: " . $invoices->count() . "\n";

        if ($invoices->count() > 0) {
            $firstInvoice = $invoices->first();
            echo "First invoice ID: " . $firstInvoice->id . "\n";
            echo "First invoice number: " . $firstInvoice->invoice_number . "\n";
            echo "First invoice amount: " . $firstInvoice->amount . "\n";
            echo "First invoice status: " . $firstInvoice->status . "\n";

            // Test 4: Check API endpoint simulation
            echo "\nTest 4: API Endpoint Simulation\n";
            echo "API Base URL: http://103.98.76.11/api\n";
            echo "Test Endpoint: http://103.98.76.11/api/tenant/test\n";
            echo "Dashboard Endpoint: http://103.98.76.11/api/tenant/dashboard\n";
            echo "Invoices Endpoint: http://103.98.76.11/api/tenant/invoices\n";
            echo "PDF Endpoint: http://103.98.76.11/api/tenant/invoices/" . $firstInvoice->id . "/pdf-file\n";

            echo "\nExpected Data:\n";
            echo "- Tenant ID: " . $firstTenant->id . "\n";
            echo "- User ID: " . $user->id . "\n";
            echo "- Invoice ID: " . $firstInvoice->id . "\n";
            echo "- Invoice Number: " . $firstInvoice->invoice_number . "\n";

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
        echo "❌ No user account found for this tenant\n";
    }
} else {
    echo "❌ No tenants found in the system\n";
}

echo "\n=== Test Complete ===\n";
echo "To test API endpoints manually:\n";
echo "1. Get authentication token from Flutter app\n";
echo "2. Test endpoints with curl or Postman:\n";
echo "   curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/test\n";
echo "   curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/dashboard\n";
echo "   curl -H 'Authorization: Bearer YOUR_TOKEN' http://103.98.76.11/api/tenant/invoices\n";
?>

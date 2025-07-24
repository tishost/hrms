<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Testing Specific Invoice ===\n\n";

// Test invoice INV-2025-0002
$invoiceNumber = 'INV-2025-0002';
echo "Looking for invoice: $invoiceNumber\n\n";

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
        echo "- Mobile: {$tenant->mobile}\n";

        // Check user for this tenant
        $user = User::where('tenant_id', $tenant->id)->first();
        if ($user) {
            echo "\n✅ User found:\n";
            echo "- ID: {$user->id}\n";
            echo "- Email: {$user->email}\n";
            echo "- Tenant ID: {$user->tenant_id}\n";

            // Check user roles
            $roles = $user->roles->pluck('name')->join(', ');
            echo "- Roles: $roles\n";

            // Test tenant relation
            if ($user->tenant) {
                echo "- Tenant relation: ✅ Working\n";
            } else {
                echo "- Tenant relation: ❌ Not working\n";
            }

        } else {
            echo "\n❌ No user found for tenant ID {$tenant->id}\n";

            // Show all users
            echo "All users:\n";
            $allUsers = User::all();
            foreach ($allUsers as $u) {
                echo "- ID: {$u->id}, Email: {$u->email}, Tenant ID: {$u->tenant_id}\n";
            }
        }
    } else {
        echo "\n❌ Tenant not found for ID {$invoice->tenant_id}\n";
    }

    // Test API endpoint simulation
    echo "\n=== API Endpoint Test ===\n";
    echo "API Base URL: http://103.98.76.11/api\n";
    echo "Test Endpoint: http://103.98.76.11/api/tenant/test\n";
    echo "Dashboard Endpoint: http://103.98.76.11/api/tenant/dashboard\n";
    echo "Invoices Endpoint: http://103.98.76.11/api/tenant/invoices\n";
    echo "PDF Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf\n";
    echo "PDF File Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf-file\n";

    echo "\nExpected Data:\n";
    echo "- Invoice ID: {$invoice->id}\n";
    echo "- Invoice Number: {$invoice->invoice_number}\n";
    echo "- Tenant ID: {$invoice->tenant_id}\n";
    if ($user) {
        echo "- User ID: {$user->id}\n";
        echo "- User Email: {$user->email}\n";
    }

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";

    // Show all invoices
    echo "\nAll invoices:\n";
    $allInvoices = Invoice::all();
    foreach ($allInvoices as $inv) {
        echo "- ID: {$inv->id}, Number: {$inv->invoice_number}, Tenant: {$inv->tenant_id}\n";
    }
}

echo "\n=== Test Complete ===\n";
?>

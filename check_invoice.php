<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

echo "=== Invoice Data Check ===\n\n";

// Check invoices
echo "1. Invoice Data:\n";
$invoiceCount = Invoice::count();
echo "Total invoices: $invoiceCount\n";

if ($invoiceCount > 0) {
    $invoices = Invoice::all();
    foreach ($invoices as $invoice) {
        echo "- ID: {$invoice->id}, Number: {$invoice->invoice_number}, Tenant: {$invoice->tenant_id}, Amount: {$invoice->amount}\n";
    }
} else {
    echo "No invoices found!\n";
}

// Check tenants
echo "\n2. Tenant Data:\n";
$tenantCount = Tenant::count();
echo "Total tenants: $tenantCount\n";

if ($tenantCount > 0) {
    $tenants = Tenant::all();
    foreach ($tenants as $tenant) {
        echo "- ID: {$tenant->id}, Name: {$tenant->first_name} {$tenant->last_name}, Mobile: {$tenant->mobile}\n";
    }
} else {
    echo "No tenants found!\n";
}

// Check users with tenant role
echo "\n3. User Data:\n";
$userCount = User::count();
echo "Total users: $userCount\n";

if ($userCount > 0) {
    $users = User::with('roles')->get();
    foreach ($users as $user) {
        $roles = $user->roles->pluck('name')->join(', ');
        echo "- ID: {$user->id}, Email: {$user->email}, Tenant ID: {$user->tenant_id}, Roles: $roles\n";
    }
} else {
    echo "No users found!\n";
}

// Check specific invoice INV-2025-0002
echo "\n4. Looking for Invoice INV-2025-0002:\n";
$specificInvoice = Invoice::where('invoice_number', 'INV-2025-0002')->first();
if ($specificInvoice) {
    echo "Found invoice:\n";
    echo "- ID: {$specificInvoice->id}\n";
    echo "- Number: {$specificInvoice->invoice_number}\n";
    echo "- Tenant ID: {$specificInvoice->tenant_id}\n";
    echo "- Amount: {$specificInvoice->amount}\n";
    echo "- Status: {$specificInvoice->status}\n";

    // Check if tenant exists
    $tenant = Tenant::find($specificInvoice->tenant_id);
    if ($tenant) {
        echo "- Tenant: {$tenant->first_name} {$tenant->last_name}\n";

        // Check if user exists for this tenant
        $user = User::where('tenant_id', $specificInvoice->tenant_id)->first();
        if ($user) {
            echo "- User: {$user->email}\n";
            echo "- User ID: {$user->id}\n";
            $roles = $user->roles->pluck('name')->join(', ');
            echo "- User Roles: $roles\n";
        } else {
            echo "- User: NOT FOUND for tenant ID {$specificInvoice->tenant_id}!\n";

            // Check if any user has this tenant_id
            $usersWithTenant = User::whereNotNull('tenant_id')->get();
            echo "Users with tenant_id:\n";
            foreach ($usersWithTenant as $u) {
                echo "  - User ID: {$u->id}, Email: {$u->email}, Tenant ID: {$u->tenant_id}\n";
            }
        }
    } else {
        echo "- Tenant: NOT FOUND!\n";
    }
} else {
    echo "Invoice INV-2025-0002 NOT FOUND!\n";

    // Show all invoice numbers
    echo "Available invoice numbers:\n";
    $allInvoices = Invoice::select('invoice_number')->get();
    foreach ($allInvoices as $invoice) {
        echo "- {$invoice->invoice_number}\n";
    }
}

echo "\n=== Check Complete ===\n";
?>

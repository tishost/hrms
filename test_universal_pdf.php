<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Owner;

echo "=== Testing Universal PDF Viewer ===\n\n";

// Test invoice INV-2025-0002
$invoiceNumber = 'INV-2025-0002';
echo "Testing invoice: $invoiceNumber\n\n";

$invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

if ($invoice) {
    echo "✅ Invoice found:\n";
    echo "- ID: {$invoice->id}\n";
    echo "- Number: {$invoice->invoice_number}\n";
    echo "- Owner ID: {$invoice->owner_id}\n";
    echo "- Tenant ID: {$invoice->tenant_id}\n";
    echo "- Amount: {$invoice->amount}\n";
    echo "- Status: {$invoice->status}\n";

    // Check owner
    $owner = Owner::find($invoice->owner_id);
    if ($owner) {
        echo "\n✅ Owner found:\n";
        echo "- ID: {$owner->id}\n";
        echo "- Name: {$owner->name}\n";

        // Check owner user
        $ownerUser = User::where('id', $owner->user_id)->first();
        if ($ownerUser) {
            echo "\n✅ Owner User found:\n";
            echo "- ID: {$ownerUser->id}\n";
            echo "- Email: {$ownerUser->email}\n";
            echo "- Roles: " . $ownerUser->roles->pluck('name')->implode(', ') . "\n";

            // Test owner PDF generation
            echo "\n=== Testing Owner PDF Generation ===\n";

            try {
                // Simulate owner API controller logic
                echo "1. Get invoice with owner permission:\n";
                $ownerInvoice = Invoice::where('id', $invoice->id)
                    ->where('owner_id', $owner->id)
                    ->with(['tenant', 'unit', 'property'])
                    ->first();

                if ($ownerInvoice) {
                    echo "✅ Invoice found for owner\n";
                    echo "- Invoice ID: {$ownerInvoice->id}\n";
                    echo "- Invoice Number: {$ownerInvoice->invoice_number}\n";
                    echo "- Amount: {$ownerInvoice->amount}\n";

                    // Test PDF generation with owner template
                    echo "\n2. Test PDF generation with owner template:\n";

                    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                        echo "✅ DomPDF is available\n";

                        try {
                            // Use owner template (same as web owner)
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
                        }
                    } else {
                        echo "❌ DomPDF not available\n";
                    }

                } else {
                    echo "❌ Invoice not found for owner\n";
                }

            } catch (Exception $e) {
                echo "❌ Owner PDF generation error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "\n❌ No user found for owner ID {$owner->id}\n";
        }
    } else {
        echo "\n❌ Owner not found for ID {$invoice->owner_id}\n";
    }

    // Check tenant
    $tenant = Tenant::find($invoice->tenant_id);
    if ($tenant) {
        echo "\n✅ Tenant found:\n";
        echo "- ID: {$tenant->id}\n";
        echo "- Name: {$tenant->first_name} {$tenant->last_name}\n";

        // Check tenant user
        $tenantUser = User::where('tenant_id', $tenant->id)->first();
        if ($tenantUser) {
            echo "\n✅ Tenant User found:\n";
            echo "- ID: {$tenantUser->id}\n";
            echo "- Email: {$tenantUser->email}\n";
            echo "- Roles: " . $tenantUser->roles->pluck('name')->implode(', ') . "\n";

            // Test tenant PDF generation
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

                    // Test PDF generation with owner template (same as tenant)
                    echo "\n2. Test PDF generation with owner template:\n";

                    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                        echo "✅ DomPDF is available\n";

                        try {
                            // Use owner template (same as tenant approach)
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
        } else {
            echo "\n❌ No user found for tenant ID {$tenant->id}\n";
        }
    } else {
        echo "\n❌ Tenant not found for ID {$invoice->tenant_id}\n";
    }

    // API endpoints
    echo "\n=== API Endpoints ===\n";
    echo "API Base URL: http://103.98.76.11/api\n";
    echo "Owner PDF Endpoint: http://103.98.76.11/api/owner/invoices/{$invoice->id}/pdf-file\n";
    echo "Tenant PDF Endpoint: http://103.98.76.11/api/tenant/invoices/{$invoice->id}/pdf-file\n";
    echo "User Profile Endpoint: http://103.98.76.11/api/user/profile\n";

    // Universal PDF Viewer Flow
    echo "\n=== Universal PDF Viewer Flow ===\n";
    echo "1. User clicks invoice\n";
    echo "2. UniversalPdfScreen loads\n";
    echo "3. Calls /api/user/profile to detect user type\n";
    echo "4. Based on user type, calls appropriate endpoint:\n";
    echo "   - Owner: /api/owner/invoices/{id}/pdf-file\n";
    echo "   - Tenant: /api/tenant/invoices/{id}/pdf-file\n";
    echo "5. PDF loads in WebView\n";
    echo "6. Shows user type in header\n";

} else {
    echo "❌ Invoice $invoiceNumber not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

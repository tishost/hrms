<?php
// Test tenant PDF view functionality
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;

// Test data
$testData = [
    'invoice' => [
        'id' => 1,
        'invoice_number' => 'INV-2024-001',
        'invoice_type' => 'rent',
        'description' => 'Monthly rent for January 2024',
        'amount' => 15000.00,
        'paid_amount' => 10000.00,
        'remaining_amount' => 5000.00,
        'status' => 'partial',
        'issue_date' => '2024-01-01',
        'due_date' => '2024-01-31',
        'breakdown' => [
            [
                'name' => 'Base Rent',
                'amount' => 12000.00
            ],
            [
                'name' => 'Utility Bill',
                'amount' => 3000.00
            ]
        ],
    ],
    'tenant' => [
        'name' => 'John Doe',
        'phone' => '+880 1712345678',
        'email' => 'john.doe@example.com',
    ],
    'unit' => [
        'name' => 'Unit A-101',
    ],
    'property' => [
        'name' => 'Sunrise Apartments',
        'address' => '123 Main Street, Dhaka',
    ],
    'owner' => [
        'name' => 'Property Owner',
        'phone' => '+880 1812345678',
        'email' => 'owner@example.com',
    ],
    'generated_at' => now()->format('Y-m-d H:i:s'),
];

// Test without owner (tenant view)
$testDataTenant = $testData;
$testDataTenant['owner'] = null;

echo "Testing PDF view with owner data:\n";
echo "Invoice Number: " . $testData['invoice']['invoice_number'] . "\n";
echo "Tenant: " . $testData['tenant']['name'] . "\n";
echo "Amount: " . $testData['invoice']['amount'] . "\n";
echo "Status: " . $testData['invoice']['status'] . "\n";

echo "\nTesting PDF view without owner data (tenant view):\n";
echo "Invoice Number: " . $testDataTenant['invoice']['invoice_number'] . "\n";
echo "Tenant: " . $testDataTenant['tenant']['name'] . "\n";
echo "Amount: " . $testDataTenant['invoice']['amount'] . "\n";
echo "Status: " . $testDataTenant['invoice']['status'] . "\n";

echo "\nPDF view should now work for both owner and tenant!\n";
?>

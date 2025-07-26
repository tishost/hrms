<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PDF Direct Test ===\n";

try {
    // Get the latest invoice
    $invoice = Invoice::latest()->first();

    if (!$invoice) {
        echo "No invoice found!\n";
        exit;
    }

    echo "Invoice ID: " . $invoice->id . "\n";
    echo "Invoice Number: " . $invoice->invoice_number . "\n";

        // Test optimized PDF generation
    $pdf = \PDF::loadView('owner.invoices.pdf', compact('invoice'));

    // Configure PDF for smaller size
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('dpi', 72);
    $pdf->setOption('image-dpi', 72);
    $pdf->setOption('image-quality', 60);
    $pdf->setOption('enable-local-file-access', false);
    $pdf->setOption('isRemoteEnabled', false);
    $pdf->setOption('isHtml5ParserEnabled', true);
    $pdf->setOption('isFontSubsettingEnabled', true);

    // Get PDF content
    $pdfContent = $pdf->output();

    echo "PDF Content Length: " . strlen($pdfContent) . " bytes\n";
    echo "PDF Content Type: " . $pdf->getMimeType() . "\n";

    // Save to file for testing
    $testFile = 'test_invoice.pdf';
    file_put_contents($testFile, $pdfContent);
    echo "PDF saved to: $testFile\n";

    // Test base64 encoding
    $base64 = base64_encode($pdfContent);
    echo "Base64 Length: " . strlen($base64) . "\n";

    echo "=== Test Completed Successfully ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

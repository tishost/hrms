<?php
// Direct test for owner PDF endpoint
echo "=== Direct Owner PDF Test ===\n\n";

// Test owner PDF endpoint directly
$ownerPdfUrl = "http://103.98.76.11/api/owner/invoices/2/pdf-file";
echo "Testing URL: $ownerPdfUrl\n\n";

// Test without authentication (should fail)
echo "1. Testing without authentication:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ownerPdfUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test with invalid token
echo "2. Testing with invalid token:\n";
$headers = [
    'Authorization: Bearer invalid_token',
    'Accept: application/pdf',
    'Content-Type: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ownerPdfUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test tenant endpoint (should fail for owner)
echo "3. Testing tenant endpoint (should fail):\n";
$tenantPdfUrl = "http://103.98.76.11/api/tenant/invoices/2/pdf-file";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenantPdfUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

echo "=== Test Complete ===\n";
echo "Expected Results:\n";
echo "- Owner PDF without auth: 401/403\n";
echo "- Owner PDF with invalid token: 401/403\n";
echo "- Tenant PDF with invalid token: 401/403\n";
echo "\nMobile app should call owner endpoint with valid token.\n";
?>

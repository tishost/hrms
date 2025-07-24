<?php
// Test mobile app API calls
echo "=== Testing Mobile App API Calls ===\n\n";

// Test user profile API
echo "1. Testing User Profile API:\n";
$userProfileUrl = "http://103.98.76.11/api/user/profile";
echo "URL: $userProfileUrl\n";

// You need to provide a valid token here
$token = "YOUR_TOKEN_HERE"; // Replace with actual token

$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userProfileUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test owner PDF API
echo "2. Testing Owner PDF API:\n";
$ownerPdfUrl = "http://103.98.76.11/api/owner/invoices/2/pdf-file";
echo "URL: $ownerPdfUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ownerPdfUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response Length: " . strlen($response) . " bytes\n";
echo "Response Preview: " . substr($response, 0, 100) . "...\n\n";

// Test tenant PDF API
echo "3. Testing Tenant PDF API:\n";
$tenantPdfUrl = "http://103.98.76.11/api/tenant/invoices/2/pdf-file";
echo "URL: $tenantPdfUrl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tenantPdfUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response Length: " . strlen($response) . " bytes\n";
echo "Response Preview: " . substr($response, 0, 100) . "...\n\n";

echo "=== Test Complete ===\n";
echo "Note: Replace YOUR_TOKEN_HERE with actual token from mobile app\n";
?>

<?php

// Test Email Login
echo "=== Email Login Test ===\n\n";

$apiUrl = 'http://103.98.76.11/api/login';

echo "Testing API URL: $apiUrl\n\n";

// Test data with email
$data = [
    'email' => 'owner@hrms.com',
    'password' => '123456'
];

echo "Test data:\n";
print_r($data);
echo "\n";

// Make API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

echo "Making API request...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response:\n$response\n\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "✅ Email login successful!\n";
        echo "Role: " . ($data['role'] ?? 'Unknown') . "\n";
        echo "Token: " . (isset($data['token']) ? 'Present' : 'Missing') . "\n";
    } else {
        echo "❌ Email login failed\n";
        echo "Error: " . ($data['error'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ API request failed with status: $httpCode\n";
}

echo "\n=== Test Complete ===\n";

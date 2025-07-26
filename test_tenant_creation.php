<?php
echo "=== Testing Tenant Creation ===\n\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test data
    $testData = [
        'first_name' => 'Test',
        'last_name' => null, // This should work now
        'gender' => 'Male',
        'mobile' => '01712345678',
        'alt_mobile' => '01812345678',
        'email' => 'test@example.com',
        'nid_number' => '1234567890',
        'address' => 'Test Address',
        'city' => 'Dhaka',
        'state' => 'Dhaka Division',
        'zip' => '1200',
        'country' => 'Bangladesh',
        'occupation' => 'Service',
        'company_name' => 'Test Company',
        'college_university' => null,
        'business_name' => null,
        'is_driver' => true,
        'driver_name' => 'Test Driver',
        'family_types' => 'Spouse,Child',
        'child_qty' => 2,
        'total_family_member' => 4,
        'building_id' => 2,
        'unit_id' => 3,
        'security_deposit' => 5000,
        'check_in_date' => '2025-08-01',
        'frequency' => 'Monthly',
        'remarks' => 'Test remarks',
        'status' => 'Active',
        'owner_id' => 2,
    ];

    echo "Test data:\n";
    foreach ($testData as $key => $value) {
        echo "$key: " . ($value ?? 'NULL') . "\n";
    }

    echo "\n=== Inserting Test Tenant ===\n";

    // Build SQL query
    $columns = implode(', ', array_keys($testData));
    $placeholders = ':' . implode(', :', array_keys($testData));

    $sql = "INSERT INTO tenants ($columns, created_at, updated_at) VALUES ($placeholders, NOW(), NOW())";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($testData);

    if ($result) {
        $tenantId = $pdo->lastInsertId();
        echo "Successfully created tenant with ID: $tenantId\n\n";

        // Verify the data
        echo "=== Verifying Created Tenant ===\n";
        $stmt = $pdo->prepare("SELECT * FROM tenants WHERE id = ?");
        $stmt->execute([$tenantId]);
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

        foreach ($tenant as $field => $value) {
            echo "$field: " . ($value ?? 'NULL') . "\n";
        }

        // Clean up - delete test tenant
        echo "\n=== Cleaning Up ===\n";
        $stmt = $pdo->prepare("DELETE FROM tenants WHERE id = ?");
        $stmt->execute([$tenantId]);
        echo "Test tenant deleted.\n";

    } else {
        echo "Failed to create tenant.\n";
        print_r($stmt->errorInfo());
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

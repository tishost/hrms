<?php
echo "Testing tenant fields...\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get latest tenant
    $stmt = $pdo->query("SELECT * FROM tenants ORDER BY id DESC LIMIT 1");
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tenant) {
        echo "Latest Tenant ID: " . $tenant['id'] . "\n";
        echo "Name: " . $tenant['first_name'] . " " . $tenant['last_name'] . "\n\n";

        echo "=== All Fields ===\n";
        foreach ($tenant as $field => $value) {
            echo "$field: " . ($value ?? 'NULL') . "\n";
        }

        echo "\n=== Database Schema ===\n";
        $stmt = $pdo->query("DESCRIBE tenants");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($columns as $column) {
            echo $column['Field'] . " - " . $column['Type'] . " - " . $column['Null'] . " - " . $column['Default'] . "\n";
        }

    } else {
        echo "No tenants found in database.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

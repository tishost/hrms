<?php
echo "=== Fixing last_name field to be nullable ===\n\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check current structure
    echo "Current last_name field structure:\n";
    $stmt = $pdo->query("DESCRIBE tenants last_name");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Field: " . $column['Field'] . ", Type: " . $column['Type'] . ", Null: " . $column['Null'] . "\n\n";

    // Make last_name nullable
    echo "Making last_name nullable...\n";
    $pdo->exec("ALTER TABLE tenants MODIFY COLUMN last_name VARCHAR(191) NULL");
    echo "Successfully made last_name nullable!\n\n";

    // Verify the change
    echo "Updated last_name field structure:\n";
    $stmt = $pdo->query("DESCRIBE tenants last_name");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Field: " . $column['Field'] . ", Type: " . $column['Type'] . ", Null: " . $column['Null'] . "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";

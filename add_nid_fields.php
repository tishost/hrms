<?php
echo "=== Adding NID Front and Back Picture Fields ===\n\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if fields already exist
    $stmt = $pdo->query("DESCRIBE tenants");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($columns, 'Field');

    echo "Current columns: " . implode(', ', $columnNames) . "\n\n";

    // Add nid_front_picture field if not exists
    if (!in_array('nid_front_picture', $columnNames)) {
        echo "Adding nid_front_picture field...\n";
        $pdo->exec("ALTER TABLE tenants ADD COLUMN nid_front_picture VARCHAR(255) NULL AFTER nid_picture");
        echo "Successfully added nid_front_picture field!\n";
    } else {
        echo "nid_front_picture field already exists.\n";
    }

    // Add nid_back_picture field if not exists
    if (!in_array('nid_back_picture', $columnNames)) {
        echo "Adding nid_back_picture field...\n";
        $pdo->exec("ALTER TABLE tenants ADD COLUMN nid_back_picture VARCHAR(255) NULL AFTER nid_front_picture");
        echo "Successfully added nid_back_picture field!\n";
    } else {
        echo "nid_back_picture field already exists.\n";
    }

    // Verify the changes
    echo "\n=== Updated Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE tenants");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        if (strpos($column['Field'], 'nid') !== false) {
            echo $column['Field'] . " - " . $column['Type'] . " - " . $column['Null'] . "\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Operation Complete ===\n";

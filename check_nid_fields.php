<?php
echo "=== Checking NID Fields in Tenants Table ===\n\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check table structure
    $stmt = $pdo->query("DESCRIBE tenants");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== Tenants Table Structure ===\n";
    foreach ($columns as $column) {
        if (strpos($column['Field'], 'nid') !== false) {
            echo "✅ " . $column['Field'] . " - " . $column['Type'] . " - " . $column['Null'] . "\n";
        }
    }

    echo "\n=== All NID Related Fields ===\n";
    $nidFields = [];
    foreach ($columns as $column) {
        if (strpos($column['Field'], 'nid') !== false) {
            $nidFields[] = $column['Field'];
        }
    }

    if (count($nidFields) > 0) {
        echo "Found " . count($nidFields) . " NID fields:\n";
        foreach ($nidFields as $field) {
            echo "  - $field\n";
        }
    } else {
        echo "❌ No NID fields found!\n";
    }

    // Check if specific fields exist
    $requiredFields = ['nid_picture', 'nid_front_picture', 'nid_back_picture'];
    echo "\n=== Field Status ===\n";
    foreach ($requiredFields as $field) {
        $exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === $field) {
                $exists = true;
                break;
            }
        }
        if ($exists) {
            echo "✅ $field - EXISTS\n";
        } else {
            echo "❌ $field - MISSING\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";

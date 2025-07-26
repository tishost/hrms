<?php
echo "=== Fixing User Owner ID ===\n\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get users with null owner_id
    $stmt = $pdo->query("SELECT id, name, email FROM users WHERE owner_id IS NULL");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Users with null owner_id:\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . ", Name: " . $user['name'] . ", Email: " . $user['email'] . "\n";

        // Find corresponding owner
        $stmt = $pdo->prepare("SELECT id FROM owners WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $owner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($owner) {
            // Update user with owner_id
            $updateStmt = $pdo->prepare("UPDATE users SET owner_id = ? WHERE id = ?");
            $updateStmt->execute([$owner['id'], $user['id']]);
            echo "  -> Updated with owner_id: " . $owner['id'] . "\n";
        } else {
            echo "  -> No owner found for this user\n";
        }
    }

    echo "\n=== Verification ===\n";
    $stmt = $pdo->query("SELECT id, name, email, owner_id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo "ID: " . $user['id'] . ", Name: " . $user['name'] . ", Email: " . $user['email'] . ", Owner ID: " . ($user['owner_id'] ?? 'NULL') . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";

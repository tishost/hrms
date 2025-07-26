<?php
echo "=== Checking User and Owner Relationship ===\n\n";

// Connect to database
$host = 'localhost';
$dbname = 'hrms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all users
    $stmt = $pdo->query("SELECT id, name, email, phone, owner_id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "=== All Users ===\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . ", Name: " . $user['name'] . ", Email: " . $user['email'] . ", Owner ID: " . ($user['owner_id'] ?? 'NULL') . "\n";
    }

    echo "\n=== All Owners ===\n";
    $stmt = $pdo->query("SELECT id, name, email, phone, user_id FROM owners");
    $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($owners as $owner) {
        echo "ID: " . $owner['id'] . ", Name: " . $owner['name'] . ", Email: " . $owner['email'] . ", User ID: " . $owner['user_id'] . "\n";
    }

    echo "\n=== User-Owner Relationship ===\n";
    $stmt = $pdo->query("
        SELECT u.id as user_id, u.name as user_name, u.email as user_email, u.owner_id,
               o.id as owner_id_from_owner, o.name as owner_name, o.user_id as owner_user_id
        FROM users u
        LEFT JOIN owners o ON u.id = o.user_id
        WHERE u.email LIKE '%owner%' OR u.email LIKE '%admin%'
    ");
    $relationships = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($relationships as $rel) {
        echo "User ID: " . $rel['user_id'] . ", Name: " . $rel['user_name'] . ", Email: " . $rel['user_email'] . "\n";
        echo "  User.owner_id: " . ($rel['owner_id'] ?? 'NULL') . "\n";
        echo "  Owner.user_id: " . ($rel['owner_user_id'] ?? 'NULL') . "\n";
        echo "  Owner ID from owners table: " . ($rel['owner_id_from_owner'] ?? 'NULL') . "\n";
        echo "---\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

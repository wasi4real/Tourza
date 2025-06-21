<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email FROM users");
    $users = $stmt->fetchAll();
    echo "<h2>Database connection successful!</h2>";
    echo "<h3>Users table:</h3>";
    if (count($users) === 0) {
        echo "<p>No users found.</p>";
    } else {
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li>{$user['id']}: {$user['first_name']} {$user['last_name']} ({$user['email']})</li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "<h2>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
?> 
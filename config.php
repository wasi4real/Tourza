<?php
// Database configuration
define('DB_HOST', 'localhost');     // Usually 'localhost' for local development
define('DB_USER', 'root');         // Your MySQL username
define('DB_PASS', '');             // Your MySQL password
define('DB_NAME', 'tourza');       // Your database name


// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 
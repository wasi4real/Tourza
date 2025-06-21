<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
require_once 'auth.php';

header('Content-Type: application/json');

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$user = $isLoggedIn ? $auth->getCurrentUser() : null;

echo json_encode([
    'isLoggedIn' => $isLoggedIn,
    'user' => $user
]);
?> 
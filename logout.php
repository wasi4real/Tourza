<?php
require_once 'auth.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->logout();

echo json_encode(['success' => true]);
?> 
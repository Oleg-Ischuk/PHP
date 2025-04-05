<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['error' => 'Only GET method is allowed']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, name, email, created_at FROM users ORDER BY id");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'users' => $users]);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Failed to get users: ' . $e->getMessage()]);
}
?>
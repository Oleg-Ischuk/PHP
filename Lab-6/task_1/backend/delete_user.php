<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    echo json_encode(['error' => 'Only DELETE method is allowed']);
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Delete failed: ' . $e->getMessage()]);
}
?>
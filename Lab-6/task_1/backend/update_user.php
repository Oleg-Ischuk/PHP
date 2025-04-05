<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(['error' => 'Only PUT method is allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['name']) || !isset($data['email'])) {
    echo json_encode(['error' => 'ID, name and email are required']);
    exit;
}

$id = $data['id'];
$name = trim($data['name']);
$email = trim($data['email']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['error' => 'User not found or no changes made']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
}
?>
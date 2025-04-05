<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

try {
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['error' => 'Invalid email or password']);
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['error' => 'Invalid email or password']);
        exit;
    }
    unset($user['password']);

    echo json_encode(['success' => true, 'user' => $user]);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
}
?>
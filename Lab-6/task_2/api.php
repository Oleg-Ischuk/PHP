<?php
header('Content-Type: application/json');
require_once 'config.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents('php://input'), true);
switch ($method) {
    case 'GET':
        // Get all notes
        $sql = "SELECT * FROM notes ORDER BY created_at DESC";
        $result = $conn->query($sql);

        $notes = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notes[] = $row;
            }
        }

        echo json_encode(['status' => 'success', 'data' => $notes]);
        break;

    case 'POST':
        if (empty($data['title']) || empty($data['content'])) {
            echo json_encode(['status' => 'error', 'message' => 'Title and content are required']);
            exit();
        }
        $title = $conn->real_escape_string($data['title']);
        $content = $conn->real_escape_string($data['content']);

        $sql = "INSERT INTO notes (title, content) VALUES ('$title', '$content')";

        if ($conn->query($sql) === TRUE) {
            $id = $conn->insert_id;
            $sql = "SELECT * FROM notes WHERE id = $id";
            $result = $conn->query($sql);
            $note = $result->fetch_assoc();

            echo json_encode(['status' => 'success', 'message' => 'Note created successfully', 'data' => $note]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error creating note: ' . $conn->error]);
        }
        break;

    case 'PUT':
        if (empty($data['id']) || empty($data['title']) || empty($data['content'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID, title, and content are required']);
            exit();
        }
        $id = $conn->real_escape_string($data['id']);
        $title = $conn->real_escape_string($data['title']);
        $content = $conn->real_escape_string($data['content']);

        $sql = "UPDATE notes SET title = '$title', content = '$content' WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $sql = "SELECT * FROM notes WHERE id = $id";
            $result = $conn->query($sql);
            $note = $result->fetch_assoc();

            echo json_encode(['status' => 'success', 'message' => 'Note updated successfully', 'data' => $note]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating note: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        if (empty($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
            exit();
        }

        $id = $conn->real_escape_string($data['id']);

        $sql = "DELETE FROM notes WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Note deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error deleting note: ' . $conn->error]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        break;
}

$conn->close();
?>
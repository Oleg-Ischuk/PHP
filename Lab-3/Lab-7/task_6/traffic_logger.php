<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'traffic_log';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
}

$ip_address = $_SERVER['REMOTE_ADDR'];
$request_time = date('Y-m-d H:i:s');
$requested_url = $_SERVER['REQUEST_URI'];


$file_path = $_SERVER['DOCUMENT_ROOT'] . $requested_url;


$http_status = 200;


if (http_response_code()) {
    $http_status = http_response_code();
}

elseif (!file_exists($file_path)) {
    // Check if it's a directory
    if (is_dir($_SERVER['DOCUMENT_ROOT'] . $requested_url)) {
        // Check for index files
        $index_files = ['index.php', 'index.html', 'index.htm'];
        $found = false;

        foreach ($index_files as $index) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $requested_url . '/' . $index)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $http_status = 404;
        }
    } else {
        $http_status = 404;
    }
}


if ($http_status == 404) {
    http_response_code(404);
}


error_log("Logging request: $requested_url with status: $http_status");


$sql = "INSERT INTO traffic_logs (ip_address, request_time, requested_url, http_status) 
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("sssi", $ip_address, $request_time, $requested_url, $http_status);
    $result = $stmt->execute();
    if (!$result) {
        error_log("Failed to log request: " . $conn->error);
    }

    $stmt->close();
} else {
    error_log("Failed to prepare statement: " . $conn->error);
}

$conn->close();
?>
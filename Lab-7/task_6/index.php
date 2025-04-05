<?php
$requested_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/Lab-7/task_6/';
$relative_path = str_replace($base_path, '', $requested_path);

if ($relative_path != 'index.php' && !file_exists(__DIR__ . '/' . $relative_path)) {
    // Set 404 status
    http_response_code(404);
}

$ip_address = $_SERVER['REMOTE_ADDR'];
$request_time = date('Y-m-d H:i:s');
$requested_url = $_SERVER['REQUEST_URI'];
$http_status = http_response_code() ?: 200;

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'traffic_log';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
} else {
    $sql = "INSERT INTO traffic_logs (ip_address, request_time, requested_url, http_status) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssi", $ip_address, $request_time, $requested_url, $http_status);
        $result = $stmt->execute();

        if (!$result) {
            error_log("Failed to log request: " . $conn->error);
        } else {
            // Debug message
            error_log("Successfully logged request: $requested_url with status: $http_status");
        }

        $stmt->close();
    } else {
        error_log("Failed to prepare statement: " . $conn->error);
    }
    $conn->close();
}
if (isset($http_status) && $http_status == 404) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>404 - Page Not Found</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                color: #333;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
            }
            .error-container {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                padding: 30px;
                max-width: 500px;
            }
            h1 {
                color: #e74c3c;
                margin-top: 0;
            }
            a {
                display: inline-block;
                margin-top: 20px;
                color: #3498db;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>404 - Page Not Found</h1>
            <p>The requested URL was not found on this server.</p>
            <a href="index.php">Return to Home</a>
        </div>
    </body>
    </html>';
    exit;
}

if ($relative_path && $relative_path != 'index.php' && file_exists(__DIR__ . '/' . $relative_path)) {
    include(__DIR__ . '/' . $relative_path);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Traffic Logger</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #35424a;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        main {
            flex: 1 0 auto;
            padding: 30px 0;
        }

        .content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 15px;
            color: #35424a;
        }

        p {
            margin-bottom: 15px;
        }

        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            margin-right: 10px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2980b9;
        }

        footer {
            flex-shrink: 0;
            background-color: #35424a;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Traffic Logger</h1>
    </div>
</header>

<main>
    <div class="container">
        <div class="content">
            <h2>Welcome to Traffic Logger</h2>
            <p>This application logs information about all requests to the server and provides statistics about HTTP status codes.</p>
            <p>Use the links below to navigate through the application:</p>

            <a href="stats.php" class="btn">View Statistics</a>
            <a href="admin_notifications.php" class="btn">Admin Notifications</a>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Traffic Logger. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
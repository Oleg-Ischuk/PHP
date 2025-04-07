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
                font-family: "Poppins", sans-serif;
                background-color: #1a1a2e;
                color: #fff;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
            }
            .error-container {
                background-color: #16213e;
                border-radius: 8px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                padding: 40px;
                max-width: 500px;
                position: relative;
                overflow: hidden;
            }
            .error-container:before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 5px;
                height: 100%;
                background: linear-gradient(to bottom, #ff6b6b, #ff8e53);
            }
            h1 {
                font-size: 3rem;
                margin-top: 0;
                margin-bottom: 20px;
                color: #ff6b6b;
            }
            p {
                font-size: 1.2rem;
                margin-bottom: 30px;
                color: #a9a9a9;
            }
            a {
                display: inline-block;
                background: linear-gradient(to right, #ff6b6b, #ff8e53);
                color: white;
                text-decoration: none;
                padding: 12px 30px;
                border-radius: 30px;
                font-weight: 600;
                letter-spacing: 1px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            }
            a:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
            }
            .error-code {
                position: absolute;
                font-size: 12rem;
                font-weight: 900;
                color: rgba(255, 255, 255, 0.03);
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: -1;
            }
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="error-container">
            <div class="error-code">404</div>
            <h1>Page Not Found</h1>
            <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --secondary: #f97316;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background-color: var(--dark);
            color: var(--light);
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100%;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--light);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-menu {
            padding: 2rem 0;
            flex: 1;
        }

        .nav-title {
            padding: 0 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            gap: 0.75rem;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--light);
            border-left-color: var(--primary);
        }

        .nav-link svg {
            width: 20px;
            height: 20px;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .page-description {
            color: var(--gray);
            font-size: 1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex: 1;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 300px;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .icon-stats {
            background-color: var(--primary);
        }

        .icon-notifications {
            background-color: var(--warning);
        }

        .card-content {
            flex: 1;
        }

        .card-description {
            color: var(--gray);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .card-action {
            margin-top: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #ea580c;
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        .footer {
            margin-top: auto;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            border-top: 1px solid #e2e8f0;
            color: var(--gray);
            font-size: 0.875rem;
            text-align: center;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 80px;
                padding: 1.5rem 0;
            }

            .sidebar-header {
                padding: 0 1rem 1.5rem;
                display: flex;
                justify-content: center;
            }

            .logo-text {
                display: none;
            }

            .nav-title, .nav-text {
                display: none;
            }

            .nav-link {
                justify-content: center;
                padding: 0.75rem;
            }

            .main-content {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem 0;
            }

            .sidebar-header {
                padding: 0 1.5rem 1rem;
            }

            .logo-text {
                display: block;
            }

            .nav-menu {
                padding: 1rem 0;
            }

            .nav-list {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-item {
                margin: 0 0.5rem;
            }

            .nav-link {
                padding: 0.5rem 1rem;
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .nav-link:hover, .nav-link.active {
                border-left-color: transparent;
                border-bottom-color: var(--primary);
            }

            .nav-text {
                display: none;
            }

            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"></path>
                        <path d="M18.4 8.64a9 9 0 1 1-4.78-4.78"></path>
                    </svg>
                </div>
                <span class="logo-text">Traffic Logger</span>
            </div>
        </div>

        <nav class="nav-menu">
            <div class="nav-title">Main Menu</div>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="stats.php" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"></path>
                            <path d="M18 17V9"></path>
                            <path d="M13 17V5"></path>
                            <path d="M8 17v-3"></path>
                        </svg>
                        <span class="nav-text">Statistics</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_notifications.php" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="nav-text">Notifications</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-description">Welcome to the Traffic Logger system. Monitor your website traffic and error rates.</p>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Traffic Statistics</h2>
                    <div class="card-icon icon-stats">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"></path>
                            <path d="M18 17V9"></path>
                            <path d="M13 17V5"></path>
                            <path d="M8 17v-3"></path>
                        </svg>
                    </div>
                </div>
                <div class="card-content">
                    <p class="card-description">View detailed statistics about your website traffic, including request counts, error rates, and recent 404 errors.</p>
                </div>
                <div class="card-action">
                    <a href="stats.php" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                        View Statistics
                    </a>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">Admin Notifications</h2>
                    <div class="card-icon icon-notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </div>
                </div>
                <div class="card-content">
                    <p class="card-description">Check notifications about high error rates, create test notifications, and view notification history.</p>
                </div>
                <div class="card-action">
                    <a href="admin_notifications.php" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                        View Notifications
                    </a>
                </div>
            </div>
        </div>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Traffic Logger. All rights reserved.</p>
        </footer>
    </main>
</div>
</body>
</html>

<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'traffic_log';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$last_24_hours = date('Y-m-d H:i:s', strtotime('-24 hours'));

$total_sql = "SELECT COUNT(*) as total FROM traffic_logs WHERE request_time >= ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("s", $last_24_hours);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_requests = $total_row['total'];

$error_sql = "SELECT COUNT(*) as errors FROM traffic_logs WHERE http_status = 404 AND request_time >= ?";
$error_stmt = $conn->prepare($error_sql);
$error_stmt->bind_param("s", $last_24_hours);
$error_stmt->execute();
$error_result = $error_stmt->get_result();
$error_row = $error_result->fetch_assoc();
$error_requests = $error_row['errors'];

$error_percentage = ($total_requests > 0) ? ($error_requests / $total_requests) * 100 : 0;

if ($error_percentage > 10) {
    $message = "Warning: 404 error rate is currently at " . number_format($error_percentage, 2) .
        "%, which exceeds the 10% threshold.\n\n" .
        "Total Requests: $total_requests\n" .
        "404 Errors: $error_requests\n\n" .
        "Please check the server logs and fix any broken links.";

    $check_sql = "SELECT COUNT(*) as count FROM admin_notifications WHERE notification_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] == 0) {
        $insert_sql = "INSERT INTO admin_notifications (notification_time, error_percentage, message) 
                      VALUES (NOW(), ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ds", $error_percentage, $message);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Traffic Statistics</title>
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
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .stat-card.total::before {
            background: var(--primary);
        }

        .stat-card.errors::before {
            background: var(--danger);
        }

        .stat-card.rate::before {
            background: var(--warning);
        }

        .stat-title {
            font-size: 0.875rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-title svg {
            width: 16px;
            height: 16px;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-description {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .alert-banner {
            background-color: #fef2f2;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border-left: 4px solid var(--danger);
        }

        .alert-icon {
            background-color: #fee2e2;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--danger);
            flex-shrink: 0;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #991b1b;
        }

        .alert-message {
            color: #b91c1c;
            font-size: 0.875rem;
        }

        .alert-action {
            margin-top: 1rem;
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

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .error-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .error-table {
            width: 100%;
            border-collapse: collapse;
        }

        .error-table th {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 1px solid #e2e8f0;
        }

        .error-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .error-table tr:last-child td {
            border-bottom: none;
        }

        .error-table tr:hover td {
            background-color: #f8fafc;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--gray);
        }

        .empty-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.5rem;
            color: var(--gray);
            opacity: 0.5;
        }

        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
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

            .stats-overview {
                grid-template-columns: 1fr;
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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .error-table-container {
                overflow-x: auto;
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
                    <a href="index.php" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="stats.php" class="nav-link active">
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
            <h1 class="page-title">Traffic Statistics</h1>
        </div>

        <div class="stats-overview">
            <div class="stat-card total">
                <div class="stat-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15V6"></path>
                        <path d="M18.5 18a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                        <path d="M12 12H3"></path>
                        <path d="M16 6H3"></path>
                        <path d="M12 18H3"></path>
                    </svg>
                    Total Requests
                </div>
                <div class="stat-value"><?php echo $total_requests; ?></div>
                <div class="stat-description">In the last 24 hours</div>
            </div>

            <div class="stat-card errors">
                <div class="stat-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    404 Errors
                </div>
                <div class="stat-value"><?php echo $error_requests; ?></div>
                <div class="stat-description">In the last 24 hours</div>
            </div>

            <div class="stat-card rate">
                <div class="stat-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7Z"></path>
                        <path d="M5 16v2"></path>
                        <path d="M19 16v2"></path>
                    </svg>
                    Error Rate
                </div>
                <div class="stat-value"><?php echo number_format($error_percentage, 2); ?>%</div>
                <div class="stat-description">Percentage of 404 errors</div>
            </div>
        </div>

        <?php if ($error_percentage > 10): ?>
            <div class="alert-banner">
                <div class="alert-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Warning: High Error Rate Detected</div>
                    <div class="alert-message">
                        The 404 error rate is above the 10% threshold. A notification has been recorded in the system.
                    </div>
                    <div class="alert-action">
                        <a href="admin_notifications.php" class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            View Notifications
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="section-header">
            <h2 class="section-title">Recent 404 Errors</h2>
        </div>

        <div class="error-table-container">
            <?php
            $recent_errors_sql = "SELECT * FROM traffic_logs WHERE http_status = 404 ORDER BY request_time DESC LIMIT 10";
            $recent_errors_result = $conn->query($recent_errors_sql);

            if ($recent_errors_result->num_rows > 0): ?>
                <table class="error-table">
                    <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Time</th>
                        <th>URL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $recent_errors_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['request_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['requested_url']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m15 9-6 6"></path>
                            <path d="m9 9 6 6"></path>
                        </svg>
                    </div>
                    <p>No recent 404 errors found.</p>
                </div>
            <?php endif; ?>
        </div>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Traffic Logger. All rights reserved.</p>
        </footer>
    </main>
</div>
</body>
</html>

<?php
$total_stmt->close();
$error_stmt->close();
$conn->close();
?>

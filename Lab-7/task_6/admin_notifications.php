<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'traffic_log';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$create_table_sql = "CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_time DATETIME NOT NULL,
    error_percentage DECIMAL(5,2) NOT NULL,
    message TEXT NOT NULL
)";
$conn->query($create_table_sql);

function check_error_rate($conn, $force_notify = false) {
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

    $should_notify = $error_percentage > 10 || $force_notify;

    $result = [
        'total_requests' => $total_requests,
        'error_requests' => $error_requests,
        'error_percentage' => $error_percentage,
        'should_notify' => $should_notify
    ];

    if ($should_notify) {
        $message = "Warning: 404 error rate is currently at " . number_format($error_percentage, 2) .
            "%, which exceeds the 10% threshold.\n\n" .
            "Total Requests: $total_requests\n" .
            "404 Errors: $error_requests\n\n" .
            "Please check the server logs and fix any broken links.";

        $insert_sql = "INSERT INTO admin_notifications (notification_time, error_percentage, message) 
                      VALUES (NOW(), ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ds", $error_percentage, $message);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    $total_stmt->close();
    $error_stmt->close();

    return $result;
}

$manual_notification_result = null;
if (isset($_POST['create_test_notification'])) {
    $manual_notification_result = check_error_rate($conn, true);
}

$current_status = check_error_rate($conn, false);

$history_sql = "SELECT * FROM admin_notifications ORDER BY notification_time DESC LIMIT 20";
$history_result = $conn->query($history_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Notifications - 404 Error Monitor</title>
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
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 2rem;
        }

        .tab {
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: var(--gray);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .status-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .status-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .status-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .metric {
            background-color: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .status-alert {
            background-color: #fee2e2;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .status-alert-icon {
            width: 24px;
            height: 24px;
            color: var(--danger);
            flex-shrink: 0;
        }

        .status-alert-content {
            flex: 1;
        }

        .status-alert-title {
            font-weight: 600;
            color: #991b1b;
            margin-bottom: 0.5rem;
        }

        .status-alert-message {
            color: #b91c1c;
            font-size: 0.875rem;
        }

        .status-success {
            background-color: #d1fae5;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .status-success-icon {
            width: 24px;
            height: 24px;
            color: var(--success);
            flex-shrink: 0;
        }

        .status-success-content {
            flex: 1;
        }

        .status-success-title {
            font-weight: 600;
            color: #065f46;
            margin-bottom: 0.5rem;
        }

        .status-success-message {
            color: #047857;
            font-size: 0.875rem;
        }

        .create-notification {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-description {
            color: var(--gray);
            font-size: 0.875rem;
            margin-bottom: 1rem;
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

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .notification-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .notification-item {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .notification-time {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .notification-rate {
            font-weight: 600;
            color: var(--danger);
        }

        .notification-message {
            white-space: pre-line;
            font-size: 0.875rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
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

            .status-metrics {
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

            .tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 0.5rem;
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
                    <a href="admin_notifications.php" class="nav-link active">
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
            <h1 class="page-title">Admin Notifications</h1>
            <p class="page-description">Monitor and manage error rate notifications</p>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="showTab('status')">Current Status</div>
            <div class="tab" onclick="showTab('create')">Create Notification</div>
            <div class="tab" onclick="showTab('history')">Notification History</div>
        </div>

        <div id="status" class="tab-content active">
            <div class="status-card">
                <div class="status-header">
                    <h2 class="status-title">System Status</h2>
                    <?php if ($current_status['error_percentage'] > 10): ?>
                        <span class="status-badge badge-danger">Warning</span>
                    <?php else: ?>
                        <span class="status-badge badge-success">Healthy</span>
                    <?php endif; ?>
                </div>

                <div class="status-metrics">
                    <div class="metric">
                        <div class="metric-value"><?php echo $current_status['total_requests']; ?></div>
                        <div class="metric-label">Total Requests (24h)</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value"><?php echo $current_status['error_requests']; ?></div>
                        <div class="metric-label">404 Errors (24h)</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value"><?php echo number_format($current_status['error_percentage'], 2); ?>%</div>
                        <div class="metric-label">Error Rate</div>
                    </div>
                </div>

                <?php if ($current_status['error_percentage'] > 10): ?>
                    <div class="status-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="status-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="status-alert-content">
                            <div class="status-alert-title">Warning: High Error Rate Detected</div>
                            <div class="status-alert-message">
                                The 404 error rate is above the 10% threshold. A notification has been recorded in the system.
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="status-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="status-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div class="status-success-content">
                            <div class="status-success-title">System is Healthy</div>
                            <div class="status-success-message">
                                The 404 error rate is below the 10% threshold. No action is required.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($manual_notification_result): ?>
                <div class="status-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="status-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div class="status-success-content">
                        <div class="status-success-title">Test Notification Created</div>
                        <div class="status-success-message">
                            A test notification has been successfully created and recorded in the system.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div id="create" class="tab-content">
            <div class="create-notification">
                <h2 class="status-title">Create Test Notification</h2>
                <div class="form-group">
                    <p class="form-description">
                        Use this form to manually create a test notification in the system. This is useful for testing notification functionality.
                    </p>
                </div>
                <form method="post">
                    <button type="submit" name="create_test_notification" class="btn btn-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        Create Test Notification
                    </button>
                </form>
            </div>
        </div>

        <div id="history" class="tab-content">
            <h2 class="status-title">Notification History</h2>

            <?php if ($history_result->num_rows > 0): ?>
                <div class="notification-list">
                    <?php while ($row = $history_result->fetch_assoc()): ?>
                        <div class="notification-item">
                            <div class="notification-header">
                                <div class="notification-time"><?php echo htmlspecialchars($row['notification_time']); ?></div>
                                <div class="notification-rate"><?php echo htmlspecialchars($row['error_percentage']); ?>% Error Rate</div>
                            </div>
                            <div class="notification-message"><?php echo nl2br(htmlspecialchars($row['message'])); ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </div>
                    <p>No notifications have been recorded yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Traffic Logger. All rights reserved.</p>
        </footer>
    </main>
</div>

<script>
    function showTab(tabId) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        // Remove active class from all tabs
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });

        // Show the selected tab content
        document.getElementById(tabId).classList.add('active');

        // Add active class to the clicked tab
        event.target.classList.add('active');
    }
</script>
</body>
</html>

<?php
$conn->close();
?>


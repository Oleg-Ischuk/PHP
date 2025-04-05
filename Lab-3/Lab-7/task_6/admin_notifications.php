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

            .card {
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                margin-bottom: 20px;
            }

            h1, h2 {
                color: #ffffff;
                margin-bottom: 15px;
            }

            .status {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .status-item {
                text-align: center;
                padding: 10px;
                flex: 1;
                min-width: 200px;
            }

            .status-value {
                font-size: 24px;
                font-weight: bold;
                color: #3498db;
                margin: 10px 0;
            }

            .alert {
                background-color: #f8d7da;
                color: #721c24;
                padding: 10px;
                border-radius: 4px;
                margin-bottom: 15px;
            }

            .success {
                background-color: #d4edda;
                color: #155724;
                padding: 10px;
                border-radius: 4px;
                margin-bottom: 15px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }

            table, th, td {
                border: 1px solid #ddd;
            }

            th, td {
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            button {
                background-color: #3498db;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }

            button:hover {
                background-color: #2980b9;
            }

            .nav {
                margin-bottom: 20px;
            }

            .nav a {
                margin-right: 15px;
                text-decoration: none;
                color: #3498db;
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
            <h1>Admin Notifications</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="nav">
                <a href="index.php">← Back to Dashboard</a>
                <a href="stats.php">View Statistics</a>
            </div>

            <div class="card">
                <h2>Current Status</h2>
                <div class="status">
                    <div class="status-item">
                        <div>Total Requests (24h)</div>
                        <div class="status-value"><?php echo $current_status['total_requests']; ?></div>
                    </div>
                    <div class="status-item">
                        <div>404 Errors (24h)</div>
                        <div class="status-value"><?php echo $current_status['error_requests']; ?></div>
                    </div>
                    <div class="status-item">
                        <div>Error Rate</div>
                        <div class="status-value"><?php echo number_format($current_status['error_percentage'], 2); ?>%</div>
                    </div>
                </div>

                <?php if ($current_status['error_percentage'] > 10): ?>
                    <div class="alert">
                        <strong>Warning!</strong> The 404 error rate is above the 10% threshold.
                        A notification has been recorded in the system.
                    </div>
                <?php else: ?>
                    <div class="success">
                        <strong>Good!</strong> The 404 error rate is below the 10% threshold.
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($manual_notification_result): ?>
                <div class="card">
                    <h2>Test Notification Result</h2>
                    <div class="success">
                        <strong>Success!</strong> Test notification has been recorded in the system.
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>Create Test Notification</h2>
                <p>Use this form to manually create a test notification in the system.</p>
                <form method="post">
                    <button type="submit" name="create_test_notification">Create Test Notification</button>
                </form>
            </div>

            <div class="card">
                <h2>Notification History</h2>
                <?php if ($history_result->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Date & Time</th>
                            <th>Error Rate</th>
                            <th>Message</th>
                        </tr>
                        <?php while ($row = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['notification_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['error_percentage']); ?>%</td>
                                <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No notifications have been recorded yet.</p>
                <?php endif; ?>
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

<?php
$conn->close();
?>
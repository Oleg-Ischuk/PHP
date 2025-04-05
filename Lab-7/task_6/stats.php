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

            table {
                width: 100%;
                border-collapse: collapse;
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

            .nav {
                margin-bottom: 20px;
            }

            .nav a {
                margin-right: 15px;
                text-decoration: none;
                color: #3498db;
            }

            .button {
                display: inline-block;
                background-color: #3498db;
                color: white;
                padding: 10px 15px;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 10px;
            }

            .button:hover {
                background-color: #2980b9;
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
            <h1>Traffic Statistics</h1>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="nav">
                <a href="index.php"> </a>
                <a href="admin_notifications.php">Admin Notifications</a>
            </div>

            <div class="card">
                <h2>Statistics (Last 24 Hours)</h2>
                <div class="status">
                    <div class="status-item">
                        <div>Total Requests</div>
                        <div class="status-value"><?php echo $total_requests; ?></div>
                    </div>
                    <div class="status-item">
                        <div>404 Errors</div>
                        <div class="status-value"><?php echo $error_requests; ?></div>
                    </div>
                    <div class="status-item">
                        <div>Error Rate</div>
                        <div class="status-value"><?php echo number_format($error_percentage, 2); ?>%</div>
                    </div>
                </div>

                <?php if ($error_percentage > 10): ?>
                    <div class="alert">
                        <strong>Warning!</strong> The 404 error rate is above the 10% threshold.
                        A notification has been recorded in the system.
                        <br>
                        <a href="admin_notifications.php" class="button">View Notifications</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Recent 404 Errors</h2>
                <?php
                $recent_errors_sql = "SELECT * FROM traffic_logs WHERE http_status = 404 ORDER BY request_time DESC LIMIT 10";
                $recent_errors_result = $conn->query($recent_errors_sql);

                if ($recent_errors_result->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>IP Address</th>
                            <th>Time</th>
                            <th>URL</th>
                        </tr>
                        <?php while ($row = $recent_errors_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['ip_address']); ?></td>
                                <td><?php echo htmlspecialchars($row['request_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['requested_url']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No recent 404 errors.</p>
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
$total_stmt->close();
$error_stmt->close();
$conn->close();
?>
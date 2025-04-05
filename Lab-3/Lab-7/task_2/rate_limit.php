<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Встановлюємо часовий пояс для України
date_default_timezone_set('Europe/Kiev');
ob_start();

$logFile = 'requests.log';
$maxRequestsPerMinute = 5;
$timeWindow = 60;
$userIP = $_SERVER['REMOTE_ADDR'];
if ($userIP === '::1' || $userIP === '127.0.0.1') {
    $userIP = '127.0.0.1';
}
$currentTime = time();
$logEntries = [];
$requestCount = 0;
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    foreach ($lines as $line) {
        if (empty($line)) continue;
        list($ip, $timestamp) = explode('|', $line);
        if ($currentTime - $timestamp <= $timeWindow) {
            $logEntries[] = [
                'ip' => $ip,
                'timestamp' => $timestamp
            ];
            if ($ip === $userIP) {
                $requestCount++;
            }
        }
    }
}
$logEntries[] = [
    'ip' => $userIP,
    'timestamp' => $currentTime
];
$requestCount++;
$newLogContent = '';
foreach ($logEntries as $entry) {
    $newLogContent .= $entry['ip'] . '|' . $entry['timestamp'] . "\n";
}
file_put_contents($logFile, $newLogContent);
$jsTimeUpdate = '
<script>
function updateCurrentTime() {
    const now = new Date();
    const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false
    };
    let formattedDate = now.toLocaleString("uk-UA", options)
        .replace(/(\d+)\.(\d+)\.(\d+),\s(\d+):(\d+):(\d+)/, "$3-$2-$1 $4:$5:$6");
    
    document.getElementById("current-time").textContent = formattedDate;
    setTimeout(updateCurrentTime, 1000);
}
document.addEventListener("DOMContentLoaded", updateCurrentTime);
</script>
';

// Перевіряємо, чи перевищено ліміт запитів
if ($requestCount > $maxRequestsPerMinute) {
    // Встановлюємо статус 429 Too Many Requests
    http_response_code(429);
    echo '<!DOCTYPE html>';
    echo '<html lang="uk">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Занадто багато запитів</title>';
    echo '<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .error-container {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 20px;
            margin-top: 50px;
        }
        h1 {
            color: #721c24;
        }
        .timer {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .info {
            background-color: #e2e3e5;
            border: 1px solid #d6d8db;
            border-radius: 5px;
            padding: 10px;
            margin-top: 20px;
            font-size: 14px;
            text-align: left;
        }
        .current-time {
            font-weight: bold;
            color: #0056b3;
        }
    </style>';
    echo $jsTimeUpdate;
    echo '</head>';
    echo '<body>';
    echo '<div class="error-container">';
    echo '<h1>429 - Занадто багато запитів</h1>';
    echo '<p>Ви перевищили ліміт запитів (5 запитів за хвилину).</p>';
    echo '<p>Будь ласка, спробуйте знову пізніше.</p>';
    echo '<div class="timer">Спробуйте через <span id="countdown">60</span> секунд</div>';
    echo '<script>
        var seconds = 60;
        function updateCountdown() {
            document.getElementById("countdown").textContent = seconds;
            if (seconds > 0) {
                seconds--;
                setTimeout(updateCountdown, 1000);
            } else {
                window.location.reload();
            }
        }
        updateCountdown();
    </script>';
    echo '</div>';
    echo '<div class="info">';
    echo '<h3>Відлагоджувальна інформація:</h3>';
    echo '<p>IP-адреса: ' . htmlspecialchars($userIP) . '</p>';
    echo '<p>Кількість запитів за останню хвилину: ' . $requestCount . '</p>';
    echo '<p>Максимальна кількість запитів: ' . $maxRequestsPerMinute . '</p>';
    echo '<p>Поточний час: <span id="current-time" class="current-time">' . date('Y-m-d H:i:s', $currentTime) . '</span></p>';
    echo '<p>Часовий пояс PHP: ' . date_default_timezone_get() . '</p>';
    echo '</div>';

    echo '</body>';
    echo '</html>';
} else {
    http_response_code(200);
    echo '<!DOCTYPE html>';
    echo '<html lang="uk">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Вітаємо</title>';
    echo '<style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .success-container {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 20px;
            margin-top: 50px;
            text-align: center;
        }
        h1 {
            color: #155724;
        }
        .counter {
            font-size: 18px;
            margin: 20px 0;
        }
        .refresh-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .refresh-btn:hover {
            background-color: #218838;
        }
        .info {
            background-color: #e2e3e5;
            border: 1px solid #d6d8db;
            border-radius: 5px;
            padding: 10px;
            margin-top: 20px;
            font-size: 14px;
            text-align: left;
        }
        .current-time {
            font-weight: bold;
            color: #0056b3;
        }
    </style>';
    echo $jsTimeUpdate;
    echo '</head>';
    echo '<body>';
    echo '<div class="success-container">';
    echo '<h1>200 - OK</h1>';
    echo '<p>Ласкаво просимо на наш сайт!</p>';
    echo '<div class="counter">Ви зробили ' . $requestCount . ' з ' . $maxRequestsPerMinute . ' дозволених запитів за хвилину.</div>';
    echo '<button class="refresh-btn" onclick="window.location.reload()">Оновити сторінку</button>';
    echo '</div>';
    echo '<div class="info">';
    echo '<h3>Відлагоджувальна інформація:</h3>';
    echo '<p>IP-адреса: ' . htmlspecialchars($userIP) . '</p>';
    echo '<p>Кількість запитів за останню хвилину: ' . $requestCount . '</p>';
    echo '<p>Максимальна кількість запитів: ' . $maxRequestsPerMinute . '</p>';
    echo '<p>Поточний час: <span id="current-time" class="current-time">' . date('Y-m-d H:i:s', $currentTime) . '</span></p>';
    echo '<p>Часовий пояс PHP: ' . date_default_timezone_get() . '</p>';
    echo '<h3>Останні запити:</h3>';
    echo '<ul>';
    $recentEntries = array_slice($logEntries, -10); // Останні 10 записів
    foreach ($recentEntries as $entry) {
        echo '<li>IP: ' . htmlspecialchars($entry['ip']) . ', Час: ' . date('Y-m-d H:i:s', $entry['timestamp']) . '</li>';
    }
    echo '</ul>';
    echo '</div>';

    echo '</body>';
    echo '</html>';
}
$content = ob_get_contents();
ob_end_flush();
?>
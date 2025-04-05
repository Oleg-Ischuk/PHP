<?php
// Встановлюємо часовий пояс
date_default_timezone_set('Europe/Kiev');

// Починаємо буферизацію виводу
ob_start();

// JavaScript для оновлення часу
$timeUpdateScript = '<script>
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
    
    // Форматуємо дату у вигляді YYYY-MM-DD HH:MM:SS
    let formattedDate = now.toLocaleString("uk-UA", options)
        .replace(/(\d+)\.(\d+)\.(\d+),\s(\d+):(\d+):(\d+)/, "$3-$2-$1 $4:$5:$6");
    
    document.getElementById("current-time").textContent = formattedDate;
    
    // Оновлюємо час кожну секунду
    setTimeout(updateCurrentTime, 1000);
}

// Запускаємо функцію оновлення часу після завантаження сторінки
document.addEventListener("DOMContentLoaded", updateCurrentTime);
</script>';

// Функція для відображення кастомної сторінки помилки
function showErrorPage($errorDetails = null) {
    global $timeUpdateScript;

    // Очищаємо буфер виводу
    ob_clean();

    // Встановлюємо HTTP статус-код 500
    http_response_code(500);

    // Розраховуємо час, коли проблема буде вирішена
    $resolutionTime = date('Y-m-d H:i:s', time() + 3600);

    // Виводимо HTML-код сторінки помилки
    echo '<!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>500 - Помилка сервера</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
                line-height: 1.6;
            }
            .error-box {
                background-color: #ffebee;
                border: 1px solid #ffcdd2;
                border-radius: 5px;
                padding: 20px;
                margin-top: 50px;
            }
            h1 {
                color: #d32f2f;
            }
            .time-info {
                background-color: #e8eaf6;
                border-radius: 5px;
                padding: 10px;
                margin: 20px 0;
                font-weight: bold;
            }
            .debug-info {
                background-color: #f5f5f5;
                border: 1px solid #e0e0e0;
                border-radius: 5px;
                padding: 10px;
                margin-top: 20px;
                text-align: left;
            }
            .current-time {
                color: #1976d2;
            }
        </style>
        ' . $timeUpdateScript . '
    </head>
    <body>
        <div class="error-box">
            <h1>500 - Внутрішня помилка сервера</h1>
            <p>Вибачте, на сервері сталася помилка.</p>
            <p>Наша команда вже працює над вирішенням проблеми.</p>
            
            <div class="time-info">
                <p>Очікуваний час вирішення: ' . $resolutionTime . '</p>
                <p>Поточний час: <span id="current-time" class="current-time">' . date('Y-m-d H:i:s') . '</span></p>
            </div>';

    // Показуємо деталі помилки в режимі відлагодження
    if ($errorDetails !== null && isset($_GET['debug'])) {
        echo '<div class="debug-info">
                <h3>Деталі помилки (для розробників):</h3>
                <p><strong>Тип:</strong> ' . $errorDetails['type'] . '</p>
                <p><strong>Повідомлення:</strong> ' . htmlspecialchars($errorDetails['message']) . '</p>
                <p><strong>Файл:</strong> ' . htmlspecialchars($errorDetails['file']) . '</p>
                <p><strong>Рядок:</strong> ' . $errorDetails['line'] . '</p>
            </div>';
    }

    echo '</div>
    </body>
    </html>';

    exit;
}

// Реєструємо функцію завершення
register_shutdown_function(function() {
    $error = error_get_last();

    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        showErrorPage($error);
    }
});

// Перевіряємо, чи потрібно симулювати помилку
if (isset($_GET['test_error'])) {
    // Симулюємо фатальну помилку
    non_existent_function();
} else {
    // Показуємо звичайну сторінку
    http_response_code(200);

    echo '<!DOCTYPE html>
    <html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Тест обробника помилок</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
                line-height: 1.6;
            }
            .success-box {
                background-color: #e8f5e9;
                border: 1px solid #c8e6c9;
                border-radius: 5px;
                padding: 20px;
                margin-top: 50px;
            }
            h1 {
                color: #2e7d32;
            }
            .button {
                display: inline-block;
                background-color: #1976d2;
                color: white;
                padding: 10px 20px;
                margin: 10px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
            }
            .button.error {
                background-color: #d32f2f;
            }
            .current-time {
                color: #1976d2;
                font-weight: bold;
            }
        </style>
        ' . $timeUpdateScript . '
    </head>
    <body>
        <div class="success-box">
            <h1>Тестування обробника помилок</h1>
            <p>Ця сторінка демонструє роботу обробника фатальних помилок.</p>
            <p>Зараз все працює нормально (статус 200 OK).</p>
            <p>Поточний час: <span id="current-time" class="current-time">' . date('Y-m-d H:i:s') . '</span></p>
            
            <div>
                <a href="?test_error=1" class="button error">Викликати фатальну помилку</a>
                <a href="?test_error=1&debug=1" class="button error">Помилка з деталями</a>
                <a href="?" class="button">Оновити сторінку</a>
            </div>
        </div>
    </body>
    </html>';
}
?>
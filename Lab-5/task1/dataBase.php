<?php
$host = 'localhost';
$dbname = 'lab5';
$user = 'root';
$password = '';

try {
    global $connection; // Додаємо global для доступу в інших файлах
    $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // Встановлює режим обробки помилок для з'єднання з базою даних, де помилки генерують винятки (exceptions)
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}
<?php
session_start();
require_once 'dataBase.php'; // Підключення до БД
global $connection;
// Перевіряємо, чи користувач авторизований
if (!isset($_SESSION['user_id'])) {
    header("Location: response_test.php");
    exit();
}

// Отримуємо ID користувача із сесії
$user_id = $_SESSION['user_id'];

try {
    // Видаляємо користувача з БД
    $stmt = $connection->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt->execute([$user_id]);

    // Перевіряємо, чи було видалення успішним
    if ($stmt->rowCount() > 0) {
        // Очистка та завершення сесії
        session_unset();
        session_destroy();

        // Перенаправлення на головну сторінку з повідомленням
        header("Location: response_test.php?message=deleteAccount");
        exit();
    } else {
        echo "Помилка: Не вдалося видалити акаунт!";
    }
} catch (PDOException $e) {
    die("Помилка при видаленні акаунту: " . $e->getMessage());
}
?>

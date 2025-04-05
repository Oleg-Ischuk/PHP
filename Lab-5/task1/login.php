<?php
session_start();
require_once 'dataBase.php'; // Підключення файлу БД
global $connection;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Перевірка, чи є дані у POST-запиті
    if (empty($_POST['email']) || empty($_POST['password'])) {
        die("Помилка: Введіть email та пароль!");
    }

    // Отримання та обробка введених даних
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Шукаємо користувача у БД
        $stmt = $connection->prepare("SELECT id_user, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Перевірка пароля
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Захист від сесійних атак
            $_SESSION['user_id'] = $user['id_user'];

            echo "Вхід успішний! <a href='response_test.php'>На головну</a>";
            exit;
        } else {
            echo "Невірний email або пароль!";
        }
    } catch (PDOException $e) {
        echo "⚠️ Помилка БД: " . $e->getMessage();
    }
}
?>

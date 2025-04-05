<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Авторизація</title>
</head>
<body>
<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo "<p>Добрий день, Admin!</p>";
    echo "<div class='button-container admin-buttons'>";
    echo "<a href='../index.html' class='back-link'>Назад</a>";
    echo "<a href='auth.php?logout=true' class='logout-button'>Вийти</a>";
    echo "</div>";
} else {
    ?>
    <a href="../index.html" class="back-link fixed-left">Назад</a>
    <div class="login-container">
        <form action="auth.php" method="post">
            <label for="login">Логін:</label>
            <input type="text" id="login" name="login" required>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Увійти</button>
        </form>
    </div>
    <?php
}
?>
</body>
</html>

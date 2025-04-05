<?php
$deleteMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $folderPath = './' . $login;

    if (file_exists($folderPath)) {
        // Видаляємо папку з усім вмістом
        deleteFolder($folderPath);
        $deleteMessage = "Папка з усім вмістом успішно видалена!";
    } else {
        $deleteMessage = "Папка з таким ім’ям не знайдена!";
    }
}

function deleteFolder($folderPath) {
    // Отримуємо всі файли та папки у вказаній директорії, за винятком '.' і '..'
    $files = array_diff(scandir($folderPath), array('.', '..'));
    foreach ($files as $file) {
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

        // Якщо це папка, рекурсивно викликаємо функцію для її видалення
        // Якщо це файл, видаляємо його
        is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
    }
    rmdir($folderPath);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видалення папки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Видалення папки</h1>
    <form method="post" class="form">
        <label for="login">Логін:</label>
        <input type="text" id="login" name="login" required><br>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Видалити папку">
    </form>

    <?php if ($deleteMessage): ?>
        <p class="message"><?php echo $deleteMessage; ?></p>
    <?php endif; ?>

    <div class="buttons">
        <a href="index.php" class="btn">Назад до створення папки</a>
        <a href="view.php" class="btn">Переглянути створені папки</a>
    </div>
</div>
</body>
</html>

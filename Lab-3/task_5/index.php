<?php
$creationMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $folderPath = './' . $login;

    if (file_exists($folderPath)) {
        $creationMessage = "Папка з таким іменем вже існує!";
    } else {
        mkdir($folderPath);
        mkdir($folderPath . '/video');
        mkdir($folderPath . '/music');
        mkdir($folderPath . '/photo');

        // Створюємо файли у підкаталогах
        file_put_contents($folderPath . '/video/sample_video.txt', 'Тут буде відео');
        file_put_contents($folderPath . '/music/sample_music.txt', 'Тут буде музика');
        file_put_contents($folderPath . '/photo/sample_photo.txt', 'Тут буде фото');

        $creationMessage = "Папка з усім вмістом успішно створена!";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Створення папки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<div class="container">
    <h1>Створення папки</h1>
    <form method="post" class="form">
        <label for="login">Логін:</label>
        <input type="text" id="login" name="login" required><br>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Створити папку">
    </form>

    <?php if ($creationMessage): ?>
        <p class="message"><?php echo $creationMessage; ?></p>
    <?php endif; ?>

    <div class="buttons">
        <a href="delete.php" class="btn">Перейти до видалення папки</a>
        <a href="view.php" class="btn">Переглянути створені папки</a>
    </div>
</div>
</body>
</html>

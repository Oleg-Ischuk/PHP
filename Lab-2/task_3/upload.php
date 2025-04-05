<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результати реєстрації</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .result-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .result-container p {
            margin: 10px 0;
            line-height: 1.5;
        }
        .photo-container {
            margin-top: 20px;
        }
        .photo-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white;
            background-color: #007bff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // htmlspecialchars --> перетворює спеціальні символи в HTML-сутності, щоб уникнути проблем з безпекою (наприклад, XSS-атаки)
    $login = htmlspecialchars($_POST['login']);
    $gender = isset($_POST['gender']) ? $_POST['gender'] : 'Не вказано';
    $city = htmlspecialchars($_POST['city']);
    $games = isset($_POST['games']) ? $_POST['games'] : [];
    // nl2br --> замінює символи нового рядка (\n) на HTML тег <br>, щоб зберегти форматування в веб-сторінці
    $about = nl2br(htmlspecialchars($_POST['about']));
    $password1 = isset($_POST['password']) ? $_POST['password'] : '';
    $password2 = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    $_SESSION['login'] = $login;
    $_SESSION['gender'] = $gender;
    $_SESSION['city'] = $city;
    $_SESSION['games'] = $games;
    $_SESSION['about'] = $about;
    $_SESSION['password'] = $password1;
    $_SESSION['confirm_password'] = $password2;

    $uploadDir = "uploads/";
    $photoPath = "";
    if (!file_exists($uploadDir)) {
        // mkdir --> створює нову директорію за вказаним шляхом
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES['photo']['name'])) {
        $photoName = basename($_FILES['photo']['name']);
        $photoPath = $uploadDir . $photoName;
        // move_uploaded_file --> переміщує завантажений файл з тимчасового місця вказаного шляхом до постійного місця на сервері
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    $_SESSION['photo'] = $photoPath;

    echo '<div class="result-container">';
    echo "<p><strong>Логін:</strong> {$login}</p>";
    echo "<p><strong>Стать:</strong> " . ($gender == 'male' ? 'чоловік' : 'жінка') . "</p>";
    echo "<p><strong>Місто:</strong> {$city}</p>";
    if (!empty($games)) {
        echo "<p><strong>Улюблені ігри:</strong> " . implode(", ", $games) . "</p>";
    }
    echo "<p><strong>Про себе:</strong> {$about}</p>";
    if (strcmp($password1, $password2) === 0) {
        echo "<p><strong>Пароль:</strong> Паролі співпадають</p>";
    } else {
        echo "<p><strong>Пароль:</strong> Паролі не співпадають (перший пароль: " . strlen($password1) . " символів, другий пароль: " . strlen($password2) . " символів)</p>";
    }

    if ($photoPath) {
        echo '<div class="photo-container">';
        echo "<p><strong>Фотографія:</strong></p>";
        echo "<img src='{$photoPath}' alt='Фото користувача'>";
        echo '</div>';
    }

    echo '<a href="task_3.php" class="back-link">Повернутися на головну сторінку</a>';
    echo '</div>';
}
?>
</body>
</html>
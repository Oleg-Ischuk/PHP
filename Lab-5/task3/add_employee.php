<?php
$host = 'localhost';
$database = 'company_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='error'>Помилка підключення: " . $e->getMessage() . "</div>");
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $salary = trim($_POST['salary']);

    if (!empty($name) && !empty($position) && is_numeric($salary)) {
        try {
            $sql = "INSERT INTO employees (name, position, salary) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $position, $salary]);

            $lastInsertId = $pdo->lastInsertId();
            $message = "<div class='success'>Працівника успішно додано! ID: " . $lastInsertId . "</div>";

            header("refresh:2; url=employees.php");
        } catch (PDOException $e) {
            $message = "<div class='error'>Помилка при додаванні: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='warning'>Будь ласка, заповніть всі поля коректно!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додавання працівника</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background: #f4f4f4;
        }
        h2 {
            color: #fff;
            background: linear-gradient(90deg, #4CAF50, #2196F3);
            padding: 10px;
            border-radius: 5px;
        }
        .container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        input, button {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input {
            background: white;
            color: black;
        }
        .button {
            background: #28a745;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        .button:hover {
            background: #218838;
        }
        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .success { background: #28a745; }
        .error { background: #dc3545; }
        .warning { background: #ffc107; color: black; }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #2196F3;
            font-weight: bold;
        }
        a:hover {
            color: #0d47a1;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Додати нового працівника</h2>
    <?= $message; ?>
    <form method="post">
        <label>Ім'я та прізвище працівника:</label>
        <input type="text" name="name" required>
        <label>Посада:</label>
        <input type="text" name="position" required>
        <label>Зарплата (грн):</label>
        <input type="number" name="salary" step="0.01" required>
        <button class="button" type="submit">Додати працівника</button>
    </form>
    <a href="display.php">Повернутися до списку працівників</a>
</div>
</body>
</html>

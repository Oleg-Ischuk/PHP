<?php
$host = 'localhost';
$dbname = 'company_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}

// Отримуємо ID працівника з URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Некоректний ID працівника!");
}
$employee_id = $_GET['id'];

// Отримуємо поточні дані працівника
try {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        die("⚠ Працівника не знайдено!");
    }
} catch (PDOException $e) {
    die("Помилка запиту: " . $e->getMessage());
}

// Обробка оновлення запису
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $salary = trim($_POST['salary']);

    if (!empty($name) && !empty($position) && is_numeric($salary)) {
        try {
            $update_stmt = $pdo->prepare("UPDATE employees SET name=?, position=?, salary=? WHERE id=?");
            $update_stmt->execute([$name, $position, $salary, $employee_id]);

            $message = "<p style='color: green;'>Дані успішно оновлено!</p>";
            header("refresh:2; url=employees.php"); // Автоматичне перенаправлення
        } catch (PDOException $e) {
            $message = "<p style='color: red;'>Помилка при оновленні: " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p style='color: orange;'>Заповніть всі поля коректно!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування працівника</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background: #f4f4f4;
        }
        .container {
            width: 400px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #fff;
            background: linear-gradient(90deg, #4CAF50, #2196F3);
            padding: 10px;
            border-radius: 5px;
        }
        input, select {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .button {
            padding: 12px 15px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            background: #4CAF50;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }
        .button:hover {
            background: #45a049;
        }
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
    <h2>Редагування працівника</h2>

    <?= $message; ?>

    <form method="post">
        <label>Ім'я працівника:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($employee['name']) ?>" required><br>

        <label>Посада:</label><br>
        <input type="text" name="position" value="<?= htmlspecialchars($employee['position']) ?>" required><br>

        <label>Зарплата (грн):</label><br>
        <input type="number" name="salary" step="0.01" value="<?= htmlspecialchars($employee['salary']) ?>" required><br>

        <button class="button" type="submit">Зберегти зміни</button>
    </form>

    <br>
    <a href="display.php">Повернутися до списку працівників</a>
</div>

</body>
</html>


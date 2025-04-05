<?php
// Параметри підключення до БД
$host = "localhost";
$user = "root";
$database = "company_db";
$password="";

try {
    // Підключення до бази даних
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Отримуємо всіх працівників
    $sql = "SELECT * FROM employees";
    $stmt = $pdo->query($sql);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}

// Обробка GET-параметра для відображення повідомлення
$message = "";
if (isset($_GET['message'])) {
    if ($_GET['message'] == "deleted") {
        $message = "<p id='message-box' class='message success'>Працівника успішно видалено!</p>";
    } elseif ($_GET['message'] == "not_found") {
        $message = "<p id='message-box' class='message warning'>Працівника з таким ID не знайдено!</p>";
    } elseif ($_GET['message'] == "error") {
        $message = "<p id='message-box' class='message error'>Помилка при видаленні працівника!</p>";
    } elseif ($_GET['message'] == "invalid") {
        $message = "<p id='message-box' class='message error'>Некоректний ID!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Список працівників</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4facfe;
            color: white;
        }
        .message {
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .warning { background-color: #fff3cd; color: #856404; }
        .error { background-color: #f8d7da; color: #721c24; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 16px;
            transition: background 0.3s;
        }
        .add { background-color: #28a745; }
        .add:hover { background-color: #218838; }
        .edit { background-color: #ffc107; }
        .edit:hover { background-color: #e0a800; }
        .delete { background-color: #dc3545; }
        .delete:hover { background-color: #c82333; }
        .stat { background-color: #17a2b8; }
        .stat:hover { background-color: #138496; }
        input[type="number"] {
            padding: 8px;
            width: 200px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .back-link {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: lightblue;
            text-decoration: none;
            border-radius: 20px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: darkblue;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="../index.html" class="back-link">Назад</a>
    <h2>Список працівників</h2>
    <?= $message ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Ім'я та прізвище</th>
            <th>Посада</th>
            <th>Зарплата (грн)</th>
        </tr>
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= htmlspecialchars($employee['id']) ?></td>
                <td><?= htmlspecialchars($employee['name']) ?></td>
                <td><?= htmlspecialchars($employee['position']) ?></td>
                <td><?= number_format($employee['salary'], 2, '.', ' ') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <button class="button add" onclick="window.location.href='add_employee.php'">Додати працівника</button>
    <form action="edit_employee.php" method="get">
        <input type="number" name="id" placeholder="Введіть ID працівника" required>
        <button class="button edit" type="submit">Редагувати працівника</button>
    </form>
    <form action="delete_employee.php" method="post">
        <input type="number" name="id" placeholder="Введіть ID працівника" required>
        <button class="button delete" type="submit">Видалити працівника</button>
    </form>
    <button class="button stat" onclick="window.location.href='statistic.php'">Переглянути статистику</button>
</div>
<script>
    setTimeout(() => {
        let messageBox = document.getElementById('message-box');
        if (messageBox) {
            messageBox.style.opacity = '0';
            setTimeout(() => messageBox.remove(), 500);
        }
    }, 3000);
</script>
</body>
</html>

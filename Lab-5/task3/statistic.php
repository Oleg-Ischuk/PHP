<?php
$host = 'localhost';
$dbname = 'company_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запит для отримання середньої зарплати
    $stmt = $pdo->query("SELECT AVG(salary) AS avg_salary FROM employees");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $avg_salary = $result['avg_salary'] ? number_format($result['avg_salary'], 2, '.', ' ') : "Немає даних";
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Статистика заробітних плат</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
            background: #f4f4f4;
        }
        .container {
            width: 50%;
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
        p {
            font-size: 20px;
            color: #333;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background: #4CAF50;
            border-radius: 5px;
            transition: 0.3s;
        }
        a:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Статистика заробітних плат</h2>
    <p>Середня заробітна плата: <strong><?= $avg_salary ?> грн</strong></p>
    <a href="display.php">Повернутися до списку працівників</a>
</div>

</body>
</html>

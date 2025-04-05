<?php
require_once '../task1/dataBase.php';
global $connection;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $category = trim($_POST['category']);

    try {
        // SQL-запит для вставки нового запису
        $sql = "INSERT INTO tov (name, description, price, category) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$name, $description, $price, $category]);

        // Отримання ID останнього вставленого запису
        $lastInsertId = $connection->lastInsertId();

        echo "<p style='color: green;'>Товар успішно додано! ID нового товару: " . $lastInsertId . "</p>";
    } catch (PDOException $e) {
        die("<p style='color: red;'>Помилка при додаванні товару: " . $e->getMessage() . "</p>");
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додавання товару</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 400px;
            margin: auto;
            border: 2px solid black;
            padding: 15px;
            text-align: center;
        }
        h2 {
            text-align: center;
        }
        .input-box {
            width: 90%;
            padding: 8px;
            margin: 5px 0;
        }
        .button {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .add-btn {
            background-color: green;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Додати новий товар</h2>

    <form method="post">
        <label>Назва товару:</label><br>
        <input class="input-box" type="text" name="name" required><br>

        <label>Опис товару:</label><br>
        <textarea class="input-box" name="description" required></textarea><br>

        <label>Ціна (грн):</label><br>
        <input class="input-box" type="number" name="price" step="0.01" required><br>

        <label>Категорія:</label><br>
        <input class="input-box" type="text" name="category" required><br>

        <button class="button add-btn" type="submit">Додати товар</button>
    </form>

    <br>
    <a href="index.php">Повернутися до списку товарів</a>
</div>

</body>
</html>

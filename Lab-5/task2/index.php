<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=lab5;charset=utf8', 'homeuser', 'admin');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}

// Отримання товарів з таблиці `tov`
try {
    $sql = "SELECT id, name, description, price, category, data_created FROM tov";
    $result = $pdo->query($sql);
} catch (PDOException $e) {
    die("Помилка виконання запиту: " . $e->getMessage());
}

// Отримання повідомлення про видалення
$message = "";
if (isset($_GET['message'])) {
    if ($_GET['message'] == "success") {
        $message = "<p style='color: green;'>Товар успішно видалено!</p>";
    } elseif ($_GET['message'] == "not_found") {
        $message = "<p style='color: orange;'>Товар з таким ID не знайдено!</p>";
    } elseif ($_GET['message'] == "error") {
        $message = "<p style='color: red;'>Помилка при видаленні товару!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <?= $message ?>
    <title>Список товарів</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 800px; margin: auto; border: 2px solid black; padding: 15px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .button { padding: 8px 12px; font-size: 14px; border: none; cursor: pointer; margin: 5px; }
        .add-btn { background-color: green; color: white; }
        .delete-btn { background-color: red; color: white; }
        .back-link {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px;
            text-decoration: none;
            color: #ffffff;
            background-color: lightblue;
            border-radius: 20px;
            transition: background-color 0.3s ease;
            z-index: 1000;
        }
        .back-link:hover {
            background-color: darkblue;
        }
    </style>
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<div class="container">
    <h2>Список товарів</h2>

    <table>
        <tr>
            <th>Номер</th>
            <th>Назва</th>
            <th>Опис</th>
            <th>Ціна (грн)</th>
            <th>Категорія</th>
            <th>Дата створення</th>
        </tr>
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= number_format($row['price'], 2, '.', ' ') ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['data_created']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <button class="button add-btn" onclick="window.location.href='insert.php'">Додати товар</button>

    <form action="delete.php" method="post">
        <input type="number" name="delete_id" placeholder="Введіть ID товару" required>
        <button class="button delete-btn" type="submit">Видалити товар</button>
    </form>
</div>

</body>
</html>

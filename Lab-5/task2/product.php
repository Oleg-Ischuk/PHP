<?php
require_once 'task1/dataBase.php';

global $connection;
try {
    $stmt = $connection->query("SELECT * FROM tov ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Помилка: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Список товарів</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid black; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h2>Список товарів</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Назва</th>
        <th>Опис</th>
        <th>Ціна (грн)</th>
        <th>Категорія</th>
        <th>Дата створення</th>
    </tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['id']) ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['description']) ?></td>
            <td><?= htmlspecialchars(number_format($product['price'], 2, '.', ' ')) ?> грн</td>
            <td><?= htmlspecialchars($product['category']) ?></td>
            <td><?= htmlspecialchars($product['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>

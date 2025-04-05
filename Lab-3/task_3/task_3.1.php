<?php
$filename = "comments.txt";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["name"]) && isset($_POST["comment"])) {
        $name = trim($_POST["name"]);
        $comment = trim($_POST["comment"]);

        if (!empty($name) && !empty($comment)) {
            $entry = "$name|$comment" . PHP_EOL;
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            if (!in_array($entry, $lines)) {
                file_put_contents($filename, $entry, FILE_APPEND);
            }
        }
    } elseif (isset($_POST["delete"])) {
        $lineToDelete = $_POST["delete"];
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        unset($lines[$lineToDelete]);
        file_put_contents($filename, implode(PHP_EOL, $lines) . PHP_EOL); // Запис оновленого вмісту до файлу
    }
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="task_3.1.css">
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<h1>Завдання 3.1</h1>
<div class="form-container">
    <h2>Залиште коментар</h2>
    <form method="post">
        <label for="name">Ім’я:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="comment">Коментар:</label>
        <textarea id="comment" name="comment" required></textarea><br><br>
        <button type="submit">Відправити</button>
    </form>
</div>
<h2>Коментарі</h2>
<table>
    <tr>
        <th>Ім’я</th>
        <th>Коментар</th>
        <th>Дія</th>
    </tr>
    <?php
    if (file_exists($filename)) {
        // Читаємо вміст файлу у масив, кожен рядок як окремий елемент
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $lineNumber => $line) {
            // Перевіряємо, чи рядок містить символ "|", який розділяє ім'я та коментар
            if (strpos($line, "|") !== false) {
                // Розділяємо рядок на дві частини: ім'я та коментар
                list($name, $comment) = explode("|", trim($line), 2);
                // Виводить рядок таблиці з іменем та коментарем, екрануючи спеціальні символи для безпеки
                echo "<tr><td>" . htmlspecialchars($name ?? '') . "</td><td>" . htmlspecialchars($comment ?? '') . "</td>";
                echo "<td><button type='button' onclick='deleteComment($lineNumber)' class='delete-button'>🗑️</button></td></tr>";
            }
        }
    }
    ?>

</table>

<script>
    function deleteComment(lineNumber) {
        var form = document.createElement('form');
        form.method = 'POST';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete';
        input.value = lineNumber;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
</script>
</body>
</html>
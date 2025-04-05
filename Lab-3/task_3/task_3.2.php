<?php
$file1 = "file1.txt";
$file2 = "file2.txt";
$fileOnlyInFirst = "only_in_first.txt";
$fileInBoth = "in_both.txt";
$fileMoreThanTwice = "more_than_twice.txt";

function getWordsFromFile($filename) {
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        return array_count_values(explode(" ", trim($content))); // Розбиваємо текст на слова та рахуємо їхню кількість
    }
    return [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['file1_content']) && isset($_POST['file2_content'])) {
    file_put_contents($file1, $_POST['file1_content']);
    file_put_contents($file2, $_POST['file2_content']);

    // Отримуємо масиви слів із файлів із зазначенням їх кількості
    $words1 = getWordsFromFile($file1);
    $words2 = getWordsFromFile($file2);

    // Знаходимо слова, які є лише у першому файлі
    $onlyInFirst = array_diff_key($words1, $words2);
    file_put_contents($fileOnlyInFirst, trim(implode("\n", array_keys($onlyInFirst))));

    // Знаходимо слова, які є в обох файлах
    $inBoth = array_intersect_key($words1, $words2);
    file_put_contents($fileInBoth, trim(implode("\n", array_keys($inBoth))));

    // Знаходимо слова, які зустрічаються більше двох разів у кожному з файлів
    $moreThanTwice = [];
    foreach ($words1 as $word => $count) {
        if ($count > 2 && isset($words2[$word]) && $words2[$word] > 2) {
            $moreThanTwice[$word] = $count;
        }
    }
    file_put_contents($fileMoreThanTwice, trim(implode("\n", array_keys($moreThanTwice))));
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_file'])) {
    $fileToDelete = $_POST['delete_file'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
    }

    file_put_contents($fileOnlyInFirst, '');
    file_put_contents($fileInBoth, '');
    file_put_contents($fileMoreThanTwice, '');
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="task_3.2.css">
    <title>Завдання 3.2: Файли зі словами</title>
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<h1>Завдання 3.2: Файли зі словами</h1>
<h2>Заповнення вмісту файлів</h2>
<form method="post">
    <h3>Вміст для Першого файлу (file1.txt):</h3>
    <textarea name="file1_content" rows="10" cols="50" placeholder="Введіть текст для першого файлу..."></textarea>

    <h3>Вміст для Другого файлу (file2.txt):</h3>
    <textarea name="file2_content" rows="10" cols="50" placeholder="Введіть текст для другого файлу..."></textarea><br><br>

    <button type="submit">Записати файли та перевірити</button>
</form>

<h2>Результати:</h2>
<div class="result-container">
    <h3>Рядки, які зустрічаються тільки в першому файлі:</h3>
    <div class="result-content">
        <?php
        if (file_exists($fileOnlyInFirst)) {
            echo nl2br(htmlspecialchars(trim(file_get_contents($fileOnlyInFirst))));
            // htmlspecialchars() - Перетворює спеціальні символи на HTML-ентіті для безпечного виведення
        }
        ?>
    </div>
</div>

<div class="result-container">
    <h3>Рядки, які зустрічаються в обох файлах:</h3>
    <div class="result-content">
        <?php
        if (file_exists($fileInBoth)) {
            echo nl2br(htmlspecialchars(trim(file_get_contents($fileInBoth))));
            // htmlspecialchars() - Перетворює спеціальні символи на HTML-ентіті для безпечного виведення
        }
        ?>
    </div>
</div>

<div class="result-container">
    <h3>Рядки, які зустрічаються в кожному файлі більше двох разів:</h3>
    <div class="result-content">
        <?php
        if (file_exists($fileMoreThanTwice)) {
            echo nl2br(htmlspecialchars(trim(file_get_contents($fileMoreThanTwice))));
            // htmlspecialchars() - Перетворює спеціальні символи на HTML-ентіті для безпечного виведення
        }
        ?>
    </div>
</div>
<h2>Вибір файлу для видалення</h2>
<form method="post">
    <label for="delete_file">Оберіть файл для видалення:</label>
    <input type="text" name="delete_file" id="delete_file" placeholder="Введіть ім'я файлу для видалення" required><br><br>
    <button type="submit">Видалити файл</button>
</form>

</body>
</html>

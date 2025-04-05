<?php
$wordsArray = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    file_put_contents('words.txt', '');
}

if (isset($_POST['submit'])) {
    if (!empty($_POST['words'])) {
        $words = $_POST['words'];
        file_put_contents('words.txt', $words);
        $fileContent = file_get_contents('words.txt');
        $wordsArray = preg_split('/\s+/', trim($fileContent));
        //$wordsArray = preg_split('/\s+/', trim($fileContent)); — це розділяє рядок $fileContent на масив слів.
        $wordsArray = array_map(function($word) {
            $word = preg_replace('/[^\w\sа-яА-ЯієїєґҐ]/u', '', $word);
            //$word = preg_replace('/[^\w\sа-яА-ЯієїєґҐ]/u', '', $word); — це видаляє всі символи з рядка $word, які не є
            // буквами (латинськими або кириличними), цифрами чи пробілами.
            return mb_strtolower($word, 'UTF-8');
        }, $wordsArray);
        usort($wordsArray, 'strcasecmp');
    } else {
        $error = "Будь ласка, введіть деякі слова.";
    }
}

if (isset($_POST['clear'])) {
    file_put_contents('words.txt', '');
    $wordsArray = [];
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Впорядкування слів</title>
    <link rel="stylesheet" href="task_3.3.css">
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<div class="container">
    <h1>Впорядкування слів за алфавітом</h1>
    <form action="task_3.3.php" method="POST">
        <label for="words">Введіть слова (розділяйте їх пробілами):</label>
        <textarea name="words" id="words" rows="5" required></textarea>
        <button type="submit" name="submit">Зберегти і впорядкувати</button>
    </form>

    <form action="task_3.3.php" method="POST">
        <button type="submit" name="clear">Очистити файл</button>
    </form>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (!empty($wordsArray)): ?>
        <h2>Впорядковані слова:</h2>
        <ul>
            <?php foreach ($wordsArray as $word): ?>
                <li><?php echo htmlspecialchars($word); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$folders = glob('*', GLOB_ONLYDIR); // Отримує всі папки в поточній директорії

function printDirectoryTree($dir) {
    $result = '<ul>';
    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $dir . '/' . $file;
        if (is_dir($filePath)) {
            $result .= '<li><strong>' . $file . '</strong>';
            $result .= printDirectoryTree($filePath);
            $result .= '</li>';
        } else {
            $result .= '<li>' . $file . '</li>';
        }
    }
    $result .= '</ul>';
    return $result;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Перегляд створених папок</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Створені папки</h1>
    <ul class="folder-list">
        <?php
        foreach ($folders as $folder) {
            echo "<li><strong>$folder</strong>";
            echo printDirectoryTree($folder);
            echo "</li>";
        }
        ?>
    </ul>

    <div class="buttons">
        <a href="index.php" class="btn">Назад до створення папки</a>
        <a href="delete.php" class="btn">Перейти до видалення папки</a>
    </div>
</div>
</body>
</html>
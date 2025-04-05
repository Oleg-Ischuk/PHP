<?php
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_dir = "uploads/";

    $target_file = $target_dir . basename($_FILES['image']['name']); //basename() — повертає ім'я файлу з повного шляху.
    $uploadOk = 1;

    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (isset($_POST['submit'])) {
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error_message = "Файл не є зображенням.<br>";
            $uploadOk = 0;
        }
    }

    if (file_exists($target_file)) {
        $error_message = "Цей файл вже існує.<br>";
        $uploadOk = 0;
    }

    if ($_FILES['image']['size'] > 5000000) {
        $error_message = "Файл занадто великий.<br>";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error_message = "Тільки JPG, JPEG, PNG та GIF файли дозволені.<br>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
    } else {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $error_message = "Файл " . htmlspecialchars(basename($_FILES['image']['name'])) . " був завантажений.<br>";
        } else {
            $error_message = "Виникла помилка при завантаженні файлу.<br>";
        }
    }
}

// Отримуємо список усіх файлів у папці "uploads", виключаючи системні записи (.. і .)
$uploaded_files = array_diff(scandir("uploads/"), array('..', '.'));
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Завантаження зображень</title>
    <link rel="stylesheet" href="task_4.css">
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<div class="container">
    <h2>Завантажити зображення</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" id="image" required>
        <input type="submit" value="Завантажити" name="submit">
    </form>
    <?php if ($error_message): ?>
        <div class="error-messages">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <h3>Завантажені файли:</h3>
    <table>
        <thead>
        <tr>
            <th>№</th>
            <th>Назва файлу</th>
            <th>Дія</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count($uploaded_files) > 0) {
            $counter = 1;
            foreach ($uploaded_files as $file) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>";
                echo "<td><a href='uploads/$file' target='_blank'>$file</a></td>";
                echo "<td><a href='uploads/$file' download>Завантажити</a></td>";
                echo "</tr>";
                $counter++;
            }
        } else {
            echo "<tr><td colspan='3'>Файли не завантажені.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

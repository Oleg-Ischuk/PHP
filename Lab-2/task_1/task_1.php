<?php
$text = '';
$find = '';
$replace = '';
$result = '';
$findMessage = '';
$findSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = $_POST['text'] ?? '';
    $find = $_POST['find'] ?? '';
    $replace = $_POST['replace'] ?? '';

    if ($find !== '') {
        // strpos --> шукає позицію першого входження підрядка у рядок
        if (strpos($text, $find) !== false) {
            $findMessage = 'Слово знайдено у тексті !';
            $findSuccess = true;
            if ($replace !== '') {
                $result = str_replace($find, $replace, $text);
                // str_replace --> замінює всі входження одного рядка іншим
            }
        } else {
            $findMessage = 'Слово не знайдено у тексті. Введіть коректне слово.';
            $findSuccess = false;
        }
    }
}
?>

<?php
$cities = '';
$sortedCities = '';
$sortMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cities = $_POST['cities'] ?? '';
    if ($cities !== '') {
        // explode --> розділяє рядок на масив, використовуючи пробіл як роздільник
        $cityArray = explode(' ', $cities);

        // sort --> сортує масив за алфавітом
        sort($cityArray);

        // implode --> об'єднує масив в рядок, додаючи пробіл між елементами
        $sortedCities = implode(' ', $cityArray);

        $sortMessage = 'Назви міст впорядковано за алфавітом.';
    }
}
?>

<?php
$filepath = '';
$filename = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filepath = trim($_POST['filepath'] ?? '');
    if ($filepath !== '') {
        // basename --> отримує ім'я файлу з повного шляху, може видаляти розширення файлу
        $basename = basename($filepath);
        // pathinfo --> отримує інформацію про шлях до файлу (каталог, ім'я файлу, розширення)
        $pathParts = pathinfo($basename);
        if (isset($pathParts['extension'])) {
            $filename = $pathParts['filename'];
        } else {
            $filename = "Файл не має розширення.";
        }
    } else {
        $filename = "Будь ласка, введіть шлях до файлу.";
    }
}
?>

<?php
$date1 = '';
$date2 = '';
$daysDifference = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Отримуємо дати з форми
    $date1 = $_POST['date1'] ?? '';
    $date2 = $_POST['date2'] ?? '';

    if ($date1 !== '' && $date2 !== '') {
        try {
            $date1Obj = DateTime::createFromFormat('d-m-Y', $date1);
            $date2Obj = DateTime::createFromFormat('d-m-Y', $date2);

            // Перевірка, чи коректний формат дат
            if ($date1Obj && $date2Obj) {
                if ($date2Obj < $date1Obj) {
                    $errorMessage = 'Друга дата не може бути меншою за першу.';
                    $daysDifference = '';
                } else {
                    $interval = $date1Obj->diff($date2Obj);
                    $daysDifference = $interval->days;
                    $errorMessage = '';
                }
            } else {
                $daysDifference = '';
                $errorMessage = 'Невірний формат дати. Використовуйте формат День-Місяць-Рік.';
            }
        } catch (Exception $e) {
            $daysDifference = '';
            $errorMessage = 'Сталася помилка при обчисленні різниці.';
        }
    } else {
        $daysDifference = '';
        $errorMessage = 'Будь ласка, введіть обидві дати.';
    }
}
?>

<?php
function generatePassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-+=<>?';
    $password = '';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, $charactersLength - 1)];
    }
    return $password;
}

function checkPasswordStrength($password) {
    if (strlen($password) < 8) {
        return 'Пароль повинен бути не менше 8 символів.';
    }
    // preg_match --> виконує пошук за регулярним виразом у рядку, повертає збіг
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Пароль повинен містити хоча б одну велику літеру.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Пароль повинен містити хоча б одну малу літеру.';
    }
    if (!preg_match('/\d/', $password)) {
        return 'Пароль повинен містити хоча б одну цифру.';
    }
    if (!preg_match('/[!@#$%^&*()_\-+=<>?]/', $password)) {
        return 'Пароль повинен містити хоча б один спеціальний символ.';
    }
    return 'Пароль є міцним.';
}

$passwordGenerated = '';
$passwordCheckMessage = '';
$passwordEntered = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // isset --> перевіряє, чи змінна існує та не є null
    if (isset($_POST['generate_password'])) {
        $passwordGenerated = generatePassword($_POST['length'] ?? 8);
    }

    if (isset($_POST['check_password'])) {
        $passwordEntered = $_POST['password'] ?? '';
        $passwordCheckMessage = checkPasswordStrength($passwordEntered);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Завдання 1.</title>
    <link rel="stylesheet" href="./task_1.css">
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<div class="container">
    <div class="forms-container">
        <h1>Завдання 1.1</h1>
        <form method="POST" class="form">
            <div class="form-elements">
                <label class="form-label" for="text">Текст:</label>
<!--                /* htmlspecialchars -> конвертує спеціальні символи в HTML-коди для безпечного виведення*/-->
                <input class="form-input" type="text" name="text" id="text" value="<?php echo htmlspecialchars($text); ?>">
            </div>
            <div class="form-elements">
                <label class="form-label" for="find">Знайти:</label>
                <input class="form-input" type="text" name="find" id="find" value="<?php echo htmlspecialchars($find); ?>">
                <div id="find-message" class="message <?php echo $findSuccess ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($findMessage); ?>
                </div>
            </div>
            <div class="form-elements">
                <label class="form-label" for="replace">Замінити:</label>
                <input class="form-input" type="text" name="replace" id="replace" value="<?php echo htmlspecialchars($replace); ?>">
            </div>
            <div class="form-elements">
                <label class="form-label" for="result">Результат:</label>
                <input class="form-input" type="text" name="result" id="result" readonly value="<?php echo htmlspecialchars($result); ?>">
            </div>
            <div class="result-button">
                <button type="submit" class="form-button">Замінити</button>
            </div>
        </form>
    </div>

    <div class="forms-container">
        <h1>Завдання 1.2</h1>
        <form method="POST" class="form">
            <div class="form-elements">
                <label class="form-label" for="cities">Назви міст (через пробіл):</label>
                <input class="form-input" type="text" name="cities" id="cities" required value="<?php echo htmlspecialchars($cities); ?>">
            </div>
            <div class="form-elements">
                <label class="form-label" for="sorted-cities">Впорядковані назви міст:</label>
                <input class="form-input" type="text" name="sorted-cities" id="sorted-cities" readonly value="<?php echo htmlspecialchars($sortedCities); ?>">
            </div>
            <div class="result-button">
                <button type="submit" class="form-button">Впорядкувати</button>
            </div>
            <div class="message success">
                <?php echo htmlspecialchars($sortMessage); ?>
            </div>
        </form>
    </div>

    <div class="forms-container">
        <h1>Завдання 1.3</h1>
        <form method="POST" class="form">
            <div class="form-elements">
                <label class="form-label" for="filepath">Введіть шлях до файлу:</label>
                <input class="form-input" type="text" name="filepath" id="filepath" value="<?php echo htmlspecialchars($filepath); ?>">
            </div>
            <div class="form-elements">
                <label class="form-label" for="filename">Ім'я файлу без розширення:</label>
                <input class="form-input" type="text" name="filename" id="filename" readonly value="<?php echo htmlspecialchars($filename); ?>">
            </div>
            <div class="result-button">
                <button type="submit" class="form-button">Отримати ім'я файлу</button>
            </div>
        </form>
    </div>

    <div class="forms-container">
        <h1>Завдання 1.4</h1>
        <form method="POST" class="form">
            <div class="form-elements">
                <label class="form-label" for="date1">Перша дата (День-Місяць-Рік):</label>
                <input class="form-input" type="text" name="date1" id="date1" value="<?php echo htmlspecialchars($date1); ?>" placeholder="Наприклад: 10-02-2015">
            </div>
            <div class="form-elements">
                <label class="form-label" for="date2">Друга дата (День-Місяць-Рік):</label>
                <input class="form-input" type="text" name="date2" id="date2" value="<?php echo htmlspecialchars($date2); ?>" placeholder="Наприклад: 15-02-2015">
            </div>
            <div class="form-elements">
                <label class="form-label" for="days-difference">Кількість днів між датами:</label>
                <input class="form-input" type="text" name="days-difference" id="days-difference" readonly value="<?php echo htmlspecialchars($daysDifference); ?>">
            </div>
            <div class="result-button">
                <button type="submit" class="form-button">Обчислити</button>
            </div>
            <?php if ($errorMessage): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <div class="forms-container">
    <h1>Завдання 1.5</h1>

    <!-- Форма для генерації пароля -->
    <form method="POST" class="form">
        <div class="form-elements">
            <label class="form-label" for="length">Довжина пароля:</label>
            <input class="form-input" type="number" name="length" id="length" value="8" min="8" max="20">
        </div>
        <div class="form-elements">
            <button type="submit" name="generate_password" class="form-button">Згенерувати пароль</button>
        </div>
        <?php if ($passwordGenerated): ?>
            <div class="form-elements">
                <label class="form-label">Згенерований пароль:</label>
                <input class="form-input" type="text" readonly value="<?php echo htmlspecialchars($passwordGenerated); ?>">
            </div>
        <?php endif; ?>
    </form>

    <!-- Форма для перевірки пароля -->
    <form method="POST" class="form">
        <div class="form-elements">
            <label class="form-label" for="password">Введіть пароль для перевірки:</label>
            <input class="form-input" type="text" name="password" id="password" value="<?php echo htmlspecialchars($passwordEntered); ?>">
        </div>
        <div class="form-elements">
            <button type="submit" name="check_password" class="form-button">Перевірити міцність</button>
        </div>
        <?php if ($passwordCheckMessage): ?>
            <div class="message <?php echo (strpos($passwordCheckMessage, 'міцним') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($passwordCheckMessage); ?>
            </div>
        <?php endif; ?>
    </form>
    </div>
</div>

</body>
</html>

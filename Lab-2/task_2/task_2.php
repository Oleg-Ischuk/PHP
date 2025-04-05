<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./task_2.css">
    <title>Завдання 2.</title>
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>

<!-- Блок 1: Пошук повторюваних елементів -->
<div class="container">
    <h2>Завдання 2.1 Знайти повторювані числа</h2>
    <form method="post">
        <input type="text" name="numbers" placeholder="Введіть числа через кому" required>
        <button type="submit" name="find_duplicates">Знайти повтори</button>
    </form>

    <?php
    function findDuplicates($arr) {
        // array_count_values --> підраховує кількість входжень кожного унікального значення в масиві
        $countValues = array_count_values($arr);
        // array_filter --> фільтрує елементи масиву за заданим умовами, повертаючи лише ті, що задовольняють умову
        $duplicates = array_filter($countValues, function($count) {
            return $count > 1;
        });
        // array_keys --> повертає всі ключі масиву, які відповідають певному значенню або всі ключі масиву
        return array_keys($duplicates);
    }

    if (isset($_POST['find_duplicates'])) {
        $input = $_POST['numbers'];
        // array_map --> застосовує функцію до кожного елемента масиву та повертає новий масив з результатами
        $numbers = array_map('trim', explode(',', $input));
        $duplicates = findDuplicates($numbers);

        echo "<p class='result'>Повторювані елементи: " . (!empty($duplicates) ? implode(', ', $duplicates) : "Немає повторень") . "</p>";
    }
    ?>
</div>

<!-- Блок 2: Генератор імен для тварин -->
<div class="container">
    <h2>Завдання 2.2 Генератор імен для тварин</h2>
    <form method="post">
        <button type="submit" name="generate_name">Згенерувати ім’я</button>
    </form>

    <?php
    function generatePetName($syllables) {
        $name = "";
        for ($i = 0; $i < rand(2, 3); $i++) {
            // array_rand --> вибирає один або кілька випадкових елементів з масиву та повертає їхні ключі
            $name .= $syllables[array_rand($syllables)];
        }
        // ucfirst --> перетворює перший символ рядка у верхній регістр, інші залишає без змін.
        return ucfirst($name);
    }

    if (isset($_POST['generate_name'])) {
        $syllables = ["Mo", "Ba", "Ki", "Lu", "Zo", "Mi", "Ra", "Ti", "Ko", "Na"];
        $petName = generatePetName($syllables);
        echo "<p class='result'>Ім’я для тваринки: <strong>$petName</strong></p>";
    }
    ?>
</div>

<!-- Блок 3: Генерація та обробка масивів -->
<div class="container">
    <h2>Завдання 2.3 Обробка двох масивів</h2>
    <form method="post">
        <button type="submit" name="process_arrays">Створити та обробити масиви</button>
    </form>

    <?php
    function createArray() {
        $length = rand(3, 7);
        $array = [];
        for ($i = 0; $i < $length; $i++) {
            $array[] = rand(10, 20);
        }
        return $array;
    }

    function mergeAndProcessArrays($arr1, $arr2) {
        // array_merge --> об'єднує два або більше масивів в один, зберігаючи значення з усіх масивів
        // array_unique --> видаляє дублікати з масиву, повертаючи масив з унікальними значеннями
        // sort --> сортує масив за зростанням
        $merged = array_merge($arr1, $arr2);
        $unique = array_unique($merged);
        sort($unique);
        return $unique;
    }

    // array_intersect --> повертає масив, що містить елементи, які є спільними для кількох масивів
    // array_values --> повертає всі значення масиву, переписуючи ключі на числові індекси
    function findCommonElements($arr1, $arr2) {
        return array_values(array_intersect($arr1, $arr2));
    }

    if (isset($_POST['process_arrays'])) {
        $array1 = createArray();
        $array2 = createArray();
        $resultArray = mergeAndProcessArrays($array1, $array2);
        $commonElements = findCommonElements($array1, $array2);

        echo "<p class='result'>Масив 1: [" . implode(', ', $array1) . "]</p>";
        echo "<p class='result'>Масив 2: [" . implode(', ', $array2) . "]</p>";
        echo "<p class='result'>Повторювані елементи в обох масивах: " . (!empty($commonElements) ? implode(', ', $commonElements) : "Немає повторень") . "</p>";
        echo "<p class='result'>Оброблений масив: [" . implode(', ', $resultArray) . "]</p>";
    }
    ?>
</div>

<!-- Блок 4: Сортування асоціативного масиву -->
<div class="container">
    <h2>Завдання 2.4 Сортування асоціативного масиву</h2>
    <form method="post">
        <button type="submit" name="sort_by_name">Сортувати за іменем</button>
        <button type="submit" name="sort_by_age">Сортувати за віком</button>
    </form>
    <?php
    $users = [
        "Alice" => 25,
        "Bob" => 22,
        "Charlie" => 30,
        "David" => 28,
        "Eve" => 24
    ];

    function sortByName(&$array) {
        ksort($array);
    }
    // ksort --> сортує масив за ключами в порядку зростання
    // asort --> сортує масив за значеннями в порядку зростання, зберігаючи відповідність між значеннями та їхніми ключами
    function sortByAge(&$array) {
        asort($array);
    }

    if (isset($_POST['sort_by_name'])) {
        sortByName($users);
        echo "<p class='result'>Відсортовані імена: " . implode(', ', array_keys($users)) . "</p>";
    } elseif (isset($_POST['sort_by_age'])) {
        sortByAge($users);
        echo "<p class='result'>Відсортований масив: <br>";
        foreach ($users as $name => $age) {
            echo "$name: $age<br>";
        }
        echo "</p>";
    }
    ?>
</div>

</body>
</html>
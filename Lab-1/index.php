<?php
echo '<div style="white-space: pre">
<h1 style="margin: 0">Завдання 1 </h1> 
Полину в мріях в купель океану,
Відчую <b>шовковистість</b> глибини,
  Чарівні мушлі з дна собі дістану,
    Щоб <b><i>взимку</i></b>
        <u>тішили</u>
            мене
               вони…
</div>';
?>


<?php echo '<h1 style="margin: 10px 0 10px 0">Завдання 2 </h1>'; ?>
<?php
function convertToUSD($uah, $usd_in_uah) {
    $usd = $uah / $usd_in_uah;
    return round($usd, 2);
}
$uah = 23456; $rate = 43;
$usd = convertToUSD($uah, $rate);
echo "<span style='font-weight: bold;'>$uah</span> грн. можна обміняти на <span style='font-weight: bold;'>$usd</span> $.";
?>

<?php echo '<h1 style="margin: 10px 0 10px 0">Завдання 3</h1>'; ?>
<?php function getSeason($month) {
    if ($month == 12 || $month == 1 || $month == 2) {
        return 'Зима';
    } elseif ($month >= 3 && $month <= 5) {
        return 'Весна';
    } elseif ($month >= 6 && $month <= 8) {
        return 'Літо';
    } elseif ($month >= 9 && $month <= 11) {
        return 'Осінь';
    } else {
        return 'Невірний номер місяця';
    }
}
$month = 4; $season = getSeason($month);
echo "Місяць номер <span style='font-weight: bold;'>$month</span> належить до сезону: <span style='font-weight: bold;'>$season</span>.";
?>

<?php echo '<h1 style="margin: 10px 0 10px 0">Завдання 4</h1>'; ?>

<?php
function checkLetter($letter) {
    $letter = mb_strtolower($letter);
    switch ($letter) {
        case 'а': case 'е': case 'є': case 'и': case 'і': case 'ї': case 'о': case 'у': case 'ю': case 'я':
        return "Голосна";
        case 'б': case 'в': case 'г': case 'ґ': case 'д': case 'ж': case 'з': case 'й': case 'к':
        case 'л': case 'м': case 'н': case 'п': case 'р': case 'с': case 'т': case 'ф': case 'х':
        case 'ц': case 'ч': case 'ш': case 'щ': case 'ь': case 'й':
        return "Приголосна";
        default:
            return "Такого символа не існує.";
    }
}
$letter = 'ь'; $result = checkLetter($letter);
echo "Буква '$letter' - $result";
?>


<?php echo '<h1 style="margin: 10px 0 10px 0">Завдання 5</h1>'; ?>

<?php
$number = mt_rand(100, 999); $digits = str_split($number);
// 1. Сума цифр
$sum = array_sum($digits);

// 2. Число, отримане виписуванням цифр в зворотному порядку
$reversedNumber = implode('', array_reverse($digits));
// implode --> перетворює елементи масиву в один рядок.

// 3. Найбільше можливе число з цих цифр
sort($digits);
$largestNumber = implode('', array_reverse($digits));

echo "Число: <span style='font-weight: bold;'>$number</span><br>";
echo "1. Сума цифр: <span style='font-weight: bold;'>$sum</span><br>";
echo "2. Число, отримане виписуванням цифр в зворотному порядку: <span style='font-weight: bold;'>$reversedNumber</span><br>";
echo "3. Найбільше можливе число: <span style='font-weight: bold;'>$largestNumber</span><br>";
?>


<?php echo '<h1 style="margin: 10px 0 10px 0">Завдання 6</h1>'; ?>
<!-- Завдання 6.1 -->
<h2>6.1 Генерація таблиці з різнокольоровими комірками</h2>
<?php
function generateTable($rows, $cols) {
    echo "<table style='border-collapse: collapse;'>";
    for ($i = 0; $i < $rows; $i++) {
        echo "<tr>";
        for ($j = 0; $j < $cols; $j++) {
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            // sprintf --> Форматує рядок, вставляючи значення змінних у вказані місця, з визначеним форматом
            echo "<td style='width: 50px; height: 50px; background-color: $color;'></td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
$rows = 5; $cols = 5;
generateTable($rows, $cols);
?>

<!-- Завдання 6.2 -->
<h2>6.2 Генерація червоних квадратів на чорному фоні</h2>

<?php
function generateRandomSquares($n) {
    echo "<div style='background-color: black; position: relative; width: 50%; height: 50%; padding: 10px;'>";
    for ($i = 0; $i < $n; $i++) {
        $size = mt_rand(50, 100);
        $x = mt_rand(0, 100 - $size);
        $y = mt_rand(0, 100 - $size);
        echo "<div style='position: absolute; width: {$size}px; height: {$size}px; background-color: red; top: {$y}vh; left: {$x}vw;'></div>";
    }

    echo "</div>";
}
$n = 5;
generateRandomSquares($n);
?>


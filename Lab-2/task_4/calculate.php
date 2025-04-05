<?php
require 'Functions/func.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $x = (float)$_POST['x'];
    $y = (float)$_POST['y'];

    $my_tg = my_tg($x);
    $sin_x = my_sin($x);
    $cos_x = my_cos($x);
    $tg_x = my_tg_func($x);
    $x_to_y = xy($x, $y);
    $factorial_x = factorial($x);

    // Підготовка результату для переадресації
    $result = [
        'x' => $x,
        'y' => $y,
        'my_tg' => $my_tg,
        'sin_x' => $sin_x,
        'cos_x' => $cos_x,
        'tg_x' => $tg_x,
        'x_to_y' => $x_to_y,
        'factorial_x' => $factorial_x
    ];
    header('Location: task_4.php?result=' . urlencode(json_encode($result)));
    // urlencode --> кодує рядок так, щоб його можна було безпечно передавати через URL, замінюючи спеціальні символи на їхні еквіваленти в коді
    // json_encode --> перетворює PHP-масив або об'єкт в JSON-формат (рядок)
    exit;
}
?>

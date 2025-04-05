<?php
function my_sin($x) {
    return sin($x);
}

function my_cos($x) {
    return cos($x);
}

function my_tg($x) {
    return tan($x);
}

function my_tg_func($x) {
    if (cos($x) == 0) {
        return "undefined";
    }
    return tan($x);
}

function xy($x, $y) {
    return pow($x, $y);
}

function factorial($x) {
    if ($x == 0 || $x == 1) {
        return 1;
    }
    $result = 1;
    for ($i = 2; $i <= $x; $i++) {
        $result *= $i;
    }
    return $result;
}
?>

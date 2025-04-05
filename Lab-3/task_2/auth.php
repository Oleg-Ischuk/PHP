<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: response_test.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if ($login === 'Admin' && $password === 'password') {
        $_SESSION['loggedin'] = true;
        header("Location: response_test.php");
        exit();
    } else {
        echo "<p>Невірний логін або пароль</p>";
        echo "<a href='response_test.php'>Спробувати знову</a>";
    }
}
?>

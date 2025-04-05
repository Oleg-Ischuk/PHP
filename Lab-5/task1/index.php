<?php
session_start();

// Якщо користувач вже увійшов, перенаправляємо на закриту сторінку
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}
?>

<?php
if (isset($_GET['message']) && $_GET['message'] == 'deleteAccount') {
    echo "<p style='color: red; position: absolute; top: 10px; left: 10px;'>Ваш акаунт було успішно видалено!</p>";
    echo "<script>
            setTimeout(function() {
                window.location.href = 'response_test.php';
            }, 1500); 
          </script>";
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизація</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            box-sizing: border-box;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        p {
            text-align: center;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .back-link {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px;
            text-decoration: none;
            color: #ffffff;
            background-color: lightblue;
            border-radius: 20px;
            transition: background-color 0.3s ease;
            z-index: 1000;
        }
        .back-link:hover {
            background-color: darkblue;
        }
    </style>
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<form action="login.php" method="post">
    <h2>Авторизація</h2>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Пароль:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Увійти">
    <p>Не маєте акаунту? <a href="register.php">Реєстрація</a></p>
</form>

</body>
</html>
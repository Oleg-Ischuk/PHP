<?php
session_start();
require_once 'dataBase.php'; // Підключення до бази даних
global $connection;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $country = !empty($_POST['country']) ? trim($_POST['country']) : null;
    $city = !empty($_POST['city']) ? trim($_POST['city']) : null;
    $street = !empty($_POST['street']) ? trim($_POST['street']) : null;
    $gender = $_POST['gender'];

    // Перевірка наявності користувача з таким email
    $stmt = $connection->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        die("Користувач з таким email вже існує!");
    }

    // Додавання користувача в базу
    $stmt = $connection->prepare("INSERT INTO users (first_name, last_name, email, password, phone, country, city, street, gender) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $password, $phone, $country, $city, $street, $gender]);

    echo "Реєстрація успішна! <a href='response_test.php'>Перейти до входу</a>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 450px;
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        select {
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
    </style>
</head>
<body>

<form action="register.php" method="post">
    <h2>Реєстрація</h2>
    <label for="first_name">Ім'я:</label>
    <input type="text" id="first_name" name="first_name" required><br><br>

    <label for="last_name">Прізвище:</label>
    <input type="text" id="last_name" name="last_name" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required><br><br>

    <label for="phone">Телефон:</label>
    <input type="text" id="phone" name="phone"><br><br>

    <label for="country">Країна:</label>
    <input type="text" id="country" name="country"><br><br>

    <label for="city">Місто:</label>
    <input type="text" id="city" name="city"><br><br>

    <label for="street">Вулиця:</label>
    <input type="text" id="street" name="street"><br><br>

    <label for="gender">Стать:</label>
    <select id="gender" name="gender" required>
        <option value="male">Чоловік</option>
        <option value="female">Жінка</option>
        <option value="other">Інше</option>
    </select><br><br>

    <input type="submit" value="Зареєструватися">
    <p>Вже маєте акаунт? <a href="index.php">Увійти</a></p>
</form>

</body>
</html>

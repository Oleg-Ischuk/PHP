<?php
session_start();
require_once 'dataBase.php';

// Перевіряємо, чи користувач увійшов
if (!isset($_SESSION['user_id'])) {
    header("Location: response_test.php");
    exit;
}

$user_id = $_SESSION['user_id'];
global $connection;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $street = trim($_POST['street']);
    $gender = $_POST['gender'];
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($new_password) {
        $updateStmt = $connection->prepare("UPDATE users SET first_name=?, last_name=?, phone=?, country=?, city=?, street=?, gender=?, password=? WHERE id_user=?");
        $updateStmt->execute([$first_name, $last_name, $phone, $country, $city, $street, $gender, $new_password, $user_id]);
    } else {
        $updateStmt = $connection->prepare("UPDATE users SET first_name=?, last_name=?, phone=?, country=?, city=?, street=?, gender=? WHERE id_user=?");
        $updateStmt->execute([$first_name, $last_name, $phone, $country, $city, $street, $gender, $user_id]);
    }

    //Зберігаємо повідомлення у сесії
    $_SESSION['success_message'] = "Дані успішно оновлено!";

    //Перенаправляємо, щоб оновити дані та уникнути повторної відправки форми
    header("Location: home.php");
    exit;
}

// Отримуємо оновлені дані користувача після змін
$stmt = $connection->prepare("SELECT first_name, last_name, phone, country, city, street, gender FROM users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Отримуємо повідомлення, якщо воно є
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : "";
unset($_SESSION['success_message']); // Очищаємо повідомлення після виводу
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Мій профіль</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 20px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .editable {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        label {
            font-weight: 500;
            color: #333;
            margin-right: 10px;
            width: 120px;
        }
        input[type="text"],
        input[type="password"] {
            padding: 8px;
            font-size: 14px;
            width: 70%;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        select{
            padding: 8px;
            font-size: 14px;
            width: 20%;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        input[type="text"]:read-only {
            background-color: #f4f4f4;
        }
        .edit-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: <?php echo $success_message ? 'block' : 'none'; ?>;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .logout-btn,
        .delete-account-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #dc3545;
            color: white;
            text-align: center;
            border: none;
            border-radius: 4px;
            margin-top: 15px;
            cursor: pointer;
        }
        .logout-btn:hover,
        .delete-account-btn:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        function enableEdit(fieldId) {
            document.getElementById(fieldId).removeAttribute('readonly');
            document.getElementById(fieldId).focus();
        }
        <?php if ($success_message): ?>
        setTimeout(function() {
            document.querySelector('.success-message').style.display = 'none';
        }, 3000);
        <?php endif; ?>
    </script>
</head>
<body>

<div class="container">
    <h2>Вітаємо, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>

    <!-- Відображення повідомлення про успішне оновлення -->
    <?php if ($success_message): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="editable">
            <label>Ім'я:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly required>
            <button type="button" class="edit-btn" onclick="enableEdit('first_name')">✏️</button>
        </div>

        <div class="editable">
            <label>Прізвище:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly required>
            <button type="button" class="edit-btn" onclick="enableEdit('last_name')">✏️</button>
        </div>

        <div class="editable">
            <label>Телефон:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly required>
            <button type="button" class="edit-btn" onclick="enableEdit('phone')">✏️</button>
        </div>

        <div class="editable">
            <label>Країна:</label>
            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" readonly>
            <button type="button" class="edit-btn" onclick="enableEdit('country')">✏️</button>
        </div>

        <div class="editable">
            <label>Місто:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" readonly>
            <button type="button" class="edit-btn" onclick="enableEdit('city')">✏️</button>
        </div>

        <div class="editable">
            <label>Вулиця:</label>
            <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($user['street']); ?>" readonly>
            <button type="button" class="edit-btn" onclick="enableEdit('street')">✏️</button>
        </div>

        <label>Стать:</label>
        <select name="gender">
            <option value="male" <?php if ($user['gender'] == 'male') echo 'selected'; ?>>Чоловік</option>
            <option value="female" <?php if ($user['gender'] == 'female') echo 'selected'; ?>>Жінка</option>
            <option value="other" <?php if ($user['gender'] == 'other') echo 'selected'; ?>>Інше</option>
        </select><br><br>

        <div class="editable">
            <label>Новий пароль:</label>
            <input type="password" id="password" name="password" readonly>
            <button type="button" class="edit-btn" onclick="enableEdit('password')">✏️</button>
        </div>

        <input type="submit" value="Зберегти зміни" class="submit-btn">
    </form>

    <form action="logout.php" method="post">
        <input type="submit" value="Вийти" class="logout-btn">
    </form>

    <form action="deleteAccount.php" method="post">
        <input type="submit" name="delete_account" value="Видалити акаунт" class="delete-account-btn"
               onclick="return confirm('Ви впевнені, що хочете видалити акаунт? Це дію неможливо скасувати!');">
    </form>
</div>

</body>
</html>

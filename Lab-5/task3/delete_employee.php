<?php
$host = 'localhost';
$dbname = 'company_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}

// Перевіряємо, чи є ID у POST-запиті
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        // Перевіряємо, чи існує працівник із таким ID
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $employee = $stmt->fetch();

        if (!$employee) {
            header("Location: display.php?message=not_found");
            exit;
        }

        // Видаляємо запис
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: display.php?message=deleted");
        exit;
    } catch (PDOException $e) {
        header("Location: display.php?message=error");
        exit;
    }
} else {
    header("Location: display.php?message=invalid");
    exit;
}
?>

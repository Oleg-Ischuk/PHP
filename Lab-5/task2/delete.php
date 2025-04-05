<?php
require_once '../task1/dataBase.php';
global $connection;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    try {
        $stmt = $connection->prepare("DELETE FROM tov WHERE id = ?");
        $stmt->execute([$delete_id]);

        if ($stmt->rowCount() > 0) {
            // Якщо видалено успішно
            header("Location: response_test.php?message=success");
        } else {
            // Якщо запис не знайдено
            header("Location: response_test.php?message=not_found");
        }
        exit;
    } catch (PDOException $e) {
        header("Location: response_test.php?message=error");
        exit;
    }
}
?>

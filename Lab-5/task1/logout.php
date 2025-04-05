<?php
session_start();
session_destroy();
header("Location: response_test.php");
exit;
?>

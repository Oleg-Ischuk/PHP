
<?php
if (isset($_GET['font'])) {
    $fontSize = $_GET['font'];
    setcookie('fontSize', $fontSize, time() + (86400 * 30), "/");
} else {
    $fontSize = isset($_COOKIE['fontSize']) ? $_COOKIE['fontSize'] : 'medium';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Font Size Example</title>
    <style>
        body {
            font-size: <?php
                switch ($fontSize) {
                    case 'large':
                        echo '36px';
                        break;
                    case 'medium':
                        echo '24px';
                        break;
                    case 'small':
                        echo '12px';
                    default:
                        echo '14px';
                }
            ?>;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        a {
            display: inline-block;
            margin: 10px;
            padding: 10px 15px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #0056b3;
        }
        .link-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .link-buttons {
            display: flex;
            gap: 10px;
        }
        .back-link {
            position: fixed;
            top: 20px;
            left: 20px;
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
<div class="link-container">
    <div class="link-buttons">
        <a href="?font=large">Великий шрифт</a>
        <a href="?font=medium">Середній шрифт</a>
        <a href="?font=small">Маленький шрифт</a>
    </div>
    <p>Це приклад тексту з вибраним розміром шрифту.</p>
</div>
</body>
</html>
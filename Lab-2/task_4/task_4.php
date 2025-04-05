<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Введення даних</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
        }
        .form-container {
            margin: 0 auto;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            align-items: center;
        }
        label{
            font-weight: bold;
            font-size: 24px;
        }
        .form-row label {
            margin-right: 10px;
        }
        .form-row input {
            flex: 1;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensure inputs don't overflow */
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #ffb500;
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
<h1>Обчислення математичних функцій</h1>

<div class="form-container">
    <form action="calculate.php" method="post">
        <div class="form-row">
            <label for="x">x:</label>
            <input type="text" id="x" name="x" required>
        </div>
        <div class="form-row">
            <label for="y">y:</label>
            <input type="text" id="y" name="y" required>
        </div>
        <input type="submit" value="Обчислити">
    </form>
</div>

<?php
if (isset($_GET['result'])) {
    // json_decode --> перетворює JSON-рядок в PHP змінну (масив або об'єкт)
    $result = json_decode($_GET['result'], true);
    if ($result) {
        echo "<h1>Результати обчислень</h1>";
        echo "<table>
                <tr>
                    <th>x</th>
                    <th>y</th>
                    <th>my_tg(x)</th>
                    <th>sin(x)</th>
                    <th>cos(x)</th>
                    <th>tg(x)</th>
                    <th>x^y</th>
                    <th>x!</th>
                </tr>
                <tr>
                    <td>{$result['x']}</td>
                    <td>{$result['y']}</td>
                    <td>{$result['my_tg']}</td>
                    <td>{$result['sin_x']}</td>
                    <td>{$result['cos_x']}</td>
                    <td>{$result['tg_x']}</td>
                    <td>{$result['x_to_y']}</td>
                    <td>{$result['factorial_x']}</td>
                </tr>
              </table>";
    }
}
?>

</body>
</html>

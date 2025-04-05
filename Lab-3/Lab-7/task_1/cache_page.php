<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$statusCode = isset($_GET['status']) ? intval($_GET['status']) : 200;

$cacheFile = 'cache.html';

// Встановлюємо HTTP статус-код
http_response_code($statusCode);

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
    $cachedContent = file_get_contents($cacheFile);

    $cachedPageMatch = preg_match('/<meta name="cached-page" content="([^"]+)">/', $cachedContent, $matches);
    $cachedPage = $cachedPageMatch ? $matches[1] : '';
    if ($cachedPage === $page) {
        echo $cachedContent;
        echo "<!-- Видано з кешу для сторінки: $page -->";
        exit;
    }
}

// Починаємо буферизацію виводу для захоплення вмісту сторінки
ob_start();

function generatePageContent($page) {
    echo '<!DOCTYPE html>';
    echo '<html lang="uk">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<meta name="cached-page" content="' . htmlspecialchars($page) . '">';
    echo '<title>Мій сайт - ' . htmlspecialchars($page) . '</title>';
    echo '<style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "Roboto", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        nav li {
            margin-right: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #3498db;
        }
        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 5px;
            flex: 1 0 auto;
            width: 100%;
            box-sizing: border-box;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #3498db;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: white;
            flex-shrink: 0;
            width: 100%;
        }
        .content-wrapper {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .news-item {
            border-left: 3px solid #3498db;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #2980b9;
        }
        .services-list li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .debug-info {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>';
    echo '</head>';
    echo '<body>';

    echo '<header>';
    echo '<nav>';
    echo '<ul>';
    echo '<li><a href="?page=home">Головна</a></li>';
    echo '<li><a href="?page=about">Про нас</a></li>';
    echo '<li><a href="?page=services">Послуги</a></li>';
    echo '<li><a href="?page=contact">Контакти</a></li>';
    echo '<li><a href="?page=nonexistent&status=404">Тест 404</a></li>';
    echo '</ul>';
    echo '</nav>';
    echo '</header>';

    echo '<div class="content-wrapper">';
    echo '<main>';

    switch ($page) {
        case 'home':
            echo '<h1>Ласкаво просимо на наш сайт</h1>';
            echo '<p>Це головна сторінка нашого сайту з динамічним вмістом.</p>';

            echo '<h2>Останні новини</h2>';
            for ($i = 1; $i <= 3; $i++) {
                echo '<div class="news-item">';
                echo '<h3>Новина #' . $i . '</h3>';
                echo '<p>Опубліковано: ' . date('d.m.Y', time() - $i * 86400) . '</p>';
                echo '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula.</p>';
                echo '</div>';
            }
            break;

        case 'about':
            echo '<h1>Про нас</h1>';
            echo '<p>Ми компанія, яка займається розробкою веб-сайтів та додатків з 2010 року.</p>';
            echo '<p>Наша команда складається з професіоналів з багаторічним досвідом у сфері веб-розробки.</p>';
            echo '<h2>Наша місія</h2>';
            echo '<p>Створювати інноваційні та якісні веб-рішення, які допомагають нашим клієнтам досягати успіху в цифровому світі.</p>';
            echo '<h2>Наші цінності</h2>';
            echo '<ul>';
            echo '<li><strong>Якість</strong> - ми прагнемо до досконалості у всьому, що робимо</li>';
            echo '<li><strong>Інновації</strong> - ми завжди шукаємо нові підходи та технології</li>';
            echo '<li><strong>Клієнтоорієнтованість</strong> - потреби клієнта завжди на першому місці</li>';
            echo '</ul>';
            break;

        case 'services':
            echo '<h1>Наші послуги</h1>';
            echo '<p>Ми пропонуємо широкий спектр послуг у сфері веб-розробки та цифрового маркетингу.</p>';
            echo '<ul class="services-list">';
            echo '<li><strong>Розробка веб-сайтів</strong> - створення сучасних, швидких та адаптивних веб-сайтів</li>';
            echo '<li><strong>Розробка мобільних додатків</strong> - створення нативних та кросплатформених мобільних додатків</li>';
            echo '<li><strong>SEO оптимізація</strong> - покращення видимості вашого сайту в пошукових системах</li>';
            echo '<li><strong>Технічна підтримка</strong> - забезпечення безперебійної роботи вашого веб-ресурсу</li>';
            echo '<li><strong>Дизайн інтерфейсів</strong> - створення зручних та привабливих інтерфейсів</li>';
            echo '</ul>';
            break;

        case 'contact':
            echo '<h1>Контакти</h1>';
            echo '<p><strong>Адреса:</strong> м. Київ, вул. Прикладна, 123</p>';
            echo '<p><strong>Телефон:</strong> +380 44 123-45-67</p>';
            echo '<p><strong>Email:</strong> info@example.com</p>';

            echo '<h2>Напишіть нам</h2>';
            echo '<form action="process_form.php" method="post">';
            echo '<div><label for="name">Ім\'я:</label>';
            echo '<input type="text" id="name" name="name" required></div>';

            echo '<div><label for="email">Email:</label>';
            echo '<input type="email" id="email" name="email" required></div>';

            echo '<div><label for="message">Повідомлення:</label>';
            echo '<textarea id="message" name="message" rows="5" required></textarea></div>';

            echo '<div><button type="submit">Надіслати</button></div>';
            echo '</form>';
            break;

        default:
            http_response_code(404);
            echo '<h1>Сторінку не знайдено</h1>';
            echo '<p>Запитана сторінка "' . htmlspecialchars($page) . '" не існує.</p>';
            echo '<p><a href="?page=home">Повернутися на головну</a></p>';
            break;
    }

    // Додаємо відлагоджувальну інформацію
    echo '<div class="debug-info">';
    echo '<h3>Відлагоджувальна інформація:</h3>';
    echo '<p>Поточна сторінка: ' . htmlspecialchars($page) . '</p>';
    echo '<p>Статус-код: ' . http_response_code() . '</p>';
    echo '<p>Файл кешу: ' . htmlspecialchars($GLOBALS['cacheFile']) . '</p>';
    echo '<p>Кеш існує: ' . (file_exists($GLOBALS['cacheFile']) ? 'Так' : 'Ні') . '</p>';
    echo '<p>Час генерації: ' . date('Y-m-d H:i:s') . '</p>';
    echo '</div>';

    echo '</main>';
    echo '</div>';

    echo '<footer>';
    echo '<p>&copy; ' . date('Y') . ' Мій сайт. Усі права захищено.</p>';
    echo '</footer>';

    echo '</body>';
    echo '</html>';
}

// Генеруємо вміст сторінки в залежності від статус-коду
if ($statusCode === 200) {
    generatePageContent($page);
} elseif ($statusCode === 404) {
    echo '<h1>Сторінку не знайдено</h1>';
    echo '<p>Запитана сторінка не може бути знайдена.</p>';
    echo '<p><a href="?page=home">Повернутися на головну</a></p>';
} else {
    echo '<h1>Статус-код: ' . $statusCode . '</h1>';
    echo '<p>Ця сторінка має власний статус-код.</p>';
}

// Отримуємо вміст з буфера виводу
$content = ob_get_contents();
ob_end_flush();

if ($statusCode === 200) {
    file_put_contents($cacheFile, $content);
    echo "<!-- Вміст закешовано у файл $cacheFile для сторінки: $page -->";
} elseif ($statusCode === 404) {
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
        echo "<!-- Файл кешу $cacheFile видалено -->";
    }
}
?>
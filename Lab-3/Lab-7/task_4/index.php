<?php
ob_start();

$redirectsFile = __DIR__ . '/redirects.json';

if (!file_exists($redirectsFile)) {
    http_response_code(500);
    echo "Configuration file not found.";
    exit;
}

$redirects = json_decode(file_get_contents($redirectsFile), true);

if (!is_array($redirects)) {
    http_response_code(500);
    echo "Invalid redirect configuration.";
    exit;
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$filePath = __DIR__ . $requestUri . '.php';
$notFoundPage = __DIR__ . '/404.php';

if (file_exists($filePath)) {
    include $filePath;
    ob_end_flush();
    exit;
}

if (isset($redirects[$requestUri])) {
    $target = $redirects[$requestUri];

    if ($target === '/404') {
        http_response_code(404);
        if (file_exists($notFoundPage)) {
            include $notFoundPage;
        } else {
            echo "404 Not Found";
        }
        exit;
    }

    if (file_exists(__DIR__ . $target . '.php')) {
        header("Location: $target", true, 301);
        exit;
    }
}

// Обробка 404
http_response_code(404);

if (file_exists($notFoundPage)) {
    include $notFoundPage;
} else {
    echo "404 Not Found";
}

ob_end_flush();

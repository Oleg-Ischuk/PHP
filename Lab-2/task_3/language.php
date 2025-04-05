<?php
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $validLangs = ['ukr', 'pl', 'en', 'de', 'fr'];
    // in_array --> перевіряє, чи існує певне значення в масиві, повертає true або false
    if (in_array($lang, $validLangs)) {
        setcookie('language', $lang, time() + 180 * 24 * 60 * 60, '/');
        // setcookie --> встановлює cookie з ім'ям 'language', значенням $lang, терміном дії 180 днів та доступністю на всьому сайті ( '/')
    }
} else {
    $lang = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'ukr';
}
switch ($lang) {
    case 'ukr':
        $languageText = "Вибрана мова: Українська";
        break;
    case 'pl':
        $languageText = "Wybierany język: Polska";
        break;
    case 'en':
        $languageText = "Selected language: English";
        break;
    case 'de':
        $languageText = "Ausgewählte Sprache: Deutsch";
        break;
    case 'fr':
        $languageText = "Langue sélectionnée: Français";
        break;
    default:
        $languageText = "Вибрана мова: Українська";
        break;
}

echo "<p>{$languageText}</p>";
?>
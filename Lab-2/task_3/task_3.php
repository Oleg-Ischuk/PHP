<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./task_3.css">
    <title>Форма реєстрації</title>
</head>
<body>
<div>
    <a href="../index.html" class="back-link">Назад</a>
</div>
<div class="language-selector">
    <a href="task_3.php?lang=ukr"><img src="https://img.icons8.com/?size=100&id=15538&format=png&color=000000" class="flags" alt="Українська"></a>
    <a href="task_3.php?lang=pl"><img src="https://img.icons8.com/?size=100&id=17964&format=png&color=000000" class="flags" alt="Polski"></a>
    <a href="task_3.php?lang=en"><img src="https://img.icons8.com/?size=100&id=15532&format=png&color=000000" class="flags" alt="English"></a>
    <a href="task_3.php?lang=de"><img src="https://img.icons8.com/?size=100&id=15502&format=png&color=000000" class="flags" alt="Deutsch"></a>
    <a href="task_3.php?lang=fr"><img src="https://img.icons8.com/?size=100&id=15497&format=png&color=000000" class="flags" alt="Français"></a>
    <?php
    if (isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        $validLangs = ['ukr', 'pl', 'en', 'de', 'fr'];
        if (in_array($lang, $validLangs)) {
            setcookie('language', $lang, time() + 180 * 24 * 60 * 60, '/');
   // setcookie --> встановлює cookie з ім'ям 'language', значенням $lang, терміном дії 180 днів та доступністю на всьому сайті ( '/')
        }
    } else {
        $lang = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'ukr';
    }

    // Масив з перекладами
    $translations = [
        'ukr' => [
            'languageText' => "Вибрана мова: Українська",
            'formTitle' => "Форма реєстрації",
            'loginLabel' => "Логін:",
            'passwordLabel' => "Пароль:",
            'confirmPasswordLabel' => "Пароль (ще раз):",
            'genderLabel' => "Стать:",
            'cityLabel' => "Місто:",
            'gamesLabel' => "Улюблені ігри:",
            'aboutLabel' => "Про себе:",
            'photoLabel' => "Фотографія:",
            'submitLabel' => "Зареєструватися",
            'male' => "чоловік",
            'female' => "жінка",
            'cityOptions' => ['Житомир', 'Київ', 'Львів', 'Одеса'],
            'gamesOptions' => ['футбол', 'баскетбол', 'волейбол', 'шахи', 'World of Tanks']
        ],
        'pl' => [
            'languageText' => "Wybierany język: Polska",
            'formTitle' => "Formularz rejestracyjny",
            'loginLabel' => "Login:",
            'passwordLabel' => "Hasło:",
            'confirmPasswordLabel' => "Hasło (ponownie):",
            'genderLabel' => "Płeć:",
            'cityLabel' => "Miasto:",
            'gamesLabel' => "Ulubione gry:",
            'aboutLabel' => "O sobie:",
            'photoLabel' => "Zdjęcie:",
            'submitLabel' => "Zarejestruj się",
            'male' => "mężczyzna",
            'female' => "kobieta",
            'cityOptions' => ['Żytomierz', 'Kijów', 'Lwów', 'Odessa'],
            'gamesOptions' => ['piłka nożna', 'koszykówka', 'siatkówka', 'szachy', 'World of Tanks']
        ],
        'en' => [
            'languageText' => "Selected language: English",
            'formTitle' => "Registration Form",
            'loginLabel' => "Login:",
            'passwordLabel' => "Password:",
            'confirmPasswordLabel' => "Confirm Password:",
            'genderLabel' => "Gender:",
            'cityLabel' => "City:",
            'gamesLabel' => "Favorite Games:",
            'aboutLabel' => "About you:",
            'photoLabel' => "Photo:",
            'submitLabel' => "Register",
            'male' => "male",
            'female' => "female",
            'cityOptions' => ['Zhytomyr', 'Kyiv', 'Lviv', 'Odessa'],
            'gamesOptions' => ['football', 'basketball', 'volleyball', 'chess', 'World of Tanks']
        ],
        'de' => [
            'languageText' => "Ausgewählte Sprache: Deutsch",
            'formTitle' => "Registrierungsformular",
            'loginLabel' => "Benutzername:",
            'passwordLabel' => "Passwort:",
            'confirmPasswordLabel' => "Passwort (nochmals):",
            'genderLabel' => "Geschlecht:",
            'cityLabel' => "Stadt:",
            'gamesLabel' => "Lieblingsspiele:",
            'aboutLabel' => "Über dich:",
            'photoLabel' => "Foto:",
            'submitLabel' => "Registrieren",
            'male' => "männlich",
            'female' => "weiblich",
            'cityOptions' => ['Schytomyr', 'Kiew', 'Lwiw', 'Odessa'],
            'gamesOptions' => ['Fußball', 'Basketball', 'Volleyball', 'Schach', 'World of Tanks']
        ],
        'fr' => [
            'languageText' => "Langue sélectionnée: Français",
            'formTitle' => "Formulaire d'inscription",
            'loginLabel' => "Identifiant:",
            'passwordLabel' => "Mot de passe:",
            'confirmPasswordLabel' => "Confirmer le mot de passe:",
            'genderLabel' => "Sexe:",
            'cityLabel' => "Ville:",
            'gamesLabel' => "Jeux préférés:",
            'aboutLabel' => "À propos de vous:",
            'photoLabel' => "Photo:",
            'submitLabel' => "S'inscrire",
            'male' => "homme",
            'female' => "femme",
            'cityOptions' => ['Jytomyr', 'Kiev', 'Lviv', 'Odessa'],
            'gamesOptions' => ['football', 'basketball', 'volley-ball', 'échecs', 'World of Tanks']
        ],
    ];

    // Вибір перекладу
    $translationsForCurrentLang = $translations[$lang];
    ?>
</div>

<div class="form-container">
    <form action="./upload.php" method="post" enctype="multipart/form-data">
        <h2><?php echo $translationsForCurrentLang['formTitle']; ?></h2>
        <table>
            <tr>
                <td><?php echo $translationsForCurrentLang['loginLabel']; ?></td>
                <td><input type="text" name="login" value="<?php echo isset($_SESSION['login']) ? $_SESSION['login'] : ''; ?>" required></td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['passwordLabel']; ?></td>
                <td><input type="password" name="password" value="<?php echo isset($_SESSION['password']) ? $_SESSION['password'] : ''; ?>" required></td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['confirmPasswordLabel']; ?></td>
                <td><input type="password" name="confirm_password" value="<?php echo isset($_SESSION['confirm_password']) ? $_SESSION['confirm_password'] : ''; ?>" required></td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['genderLabel']; ?></td>
                <td>
                    <input type="radio" name="gender" value="male" <?php echo (isset($_SESSION['gender']) && $_SESSION['gender'] == 'male') ? 'checked' : ''; ?>> <?php echo $translationsForCurrentLang['male']; ?>
                    <input type="radio" name="gender" value="female" <?php echo (isset($_SESSION['gender']) && $_SESSION['gender'] == 'female') ? 'checked' : ''; ?>> <?php echo $translationsForCurrentLang['female']; ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['cityLabel']; ?></td>
                <td>
                    <select name="city">
                        <?php foreach ($translationsForCurrentLang['cityOptions'] as $city): ?>
                            <option <?php echo (isset($_SESSION['city']) && $_SESSION['city'] == $city) ? 'selected' : ''; ?>><?php echo $city; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['gamesLabel']; ?></td>
                <td>
                    <?php foreach ($translationsForCurrentLang['gamesOptions'] as $game): ?>
                        <input type="checkbox" name="games[]" value="<?php echo $game; ?>" <?php echo (isset($_SESSION['games']) && in_array($game, $_SESSION['games'])) ? 'checked' : ''; ?>> <?php echo $game; ?><br>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['aboutLabel']; ?></td>
                <td><textarea name="about"><?php echo isset($_SESSION['about']) ? $_SESSION['about'] : ''; ?></textarea></td>
            </tr>
            <tr>
                <td><?php echo $translationsForCurrentLang['photoLabel']; ?></td>
                <td><input type="file" name="photo"></td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit"><?php echo $translationsForCurrentLang['submitLabel']; ?></button></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>

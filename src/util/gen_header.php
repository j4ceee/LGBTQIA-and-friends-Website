<?php /** @noinspection CssUnknownTarget */
function template_header($dbConnection, string $lang, $lang_title = "home"): void
{
    // TODO: support for switching between dark and light mode
    // TODO: support for changing the language

    $PDO = $dbConnection->getConnection();

    require(__DIR__ . '/auth_login_check.php'); // check if user is logged in
    /* @var bool $loggedIn */

    $index = '';
    $calendar = '';

    switch ($lang_title) {
        case 'home':
            $index = 'class="active"';
            break;
        case 'cal':
            $calendar = 'class="active"';
            break;
        default:
            break;
    }

    $title = lang_strings[$lang_title];

    echo <<<EOT
    <!DOCTYPE html>
    <html lang="$lang">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    EOT;
    if ($lang_title !== 'home') {
        echo "<title>$title — ".lang_strings['title']."</title>";
    } else {
        echo "<title>".lang_strings['title']." | ".lang_strings['uni']."</title>";
    }

    echo <<<EOT
        <link rel="stylesheet" href="./css/style_common.css">
        <link rel="icon" type="image/png" sizes="32x32" href="../img/lgbt_bunny.png">
    </head>
    <body>
    <div class="page_wrap">
        <header>
            <div class="cont_logo_nav">
                <nav class="navbar">
    EOT;
    echo '<a href="./index.php" '.$index.'>'.lang_strings['home'].'</a>';
    echo '<a href="./calendar.php" '.$calendar.'>'.lang_strings['cal'].'</a>';


    if ($PDO != null && (!$dbConnection->checkDBExists() || $dbConnection->checkDBSchema() !== true)) {
        echo '<a href="./util/setup_db.php">Setup DB</a>';
    }

    echo <<<EOT
                </nav>
            
                <div class="logo-header">
                    <a href="./index.php">
                        <img class="logo" src="./img/lgbt_bunny_white.svg" alt="Logo">
                    </a>
                </div>
            </div>
            <button class="auth_button" id="auth_button" onclick="toggleAuthWindow()">
                <div class="auth_icon" id="auth_icon" style="mask: url(./img/noun-user-6714086-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-user-6714086-grey.svg) no-repeat center / contain" ></div>
            </button>
    </header>
    EOT;

    if (!$loggedIn) {
        // collect all lang strings for the auth form
        $l_login = lang_strings['login'];
        $l_user = lang_strings['user'];
        $l_email = lang_strings['email'];
        $l_pass = lang_strings['pass'];

        echo <<<EOT
        <form class="auth_form" id="auth_window" action="./util/auth_login.php" method="post">
        <fieldset class="auth_fieldset">
            <legend>$l_login</legend>
            <div class="auth_input_cont">
                <input type="text" class="win_dark_input win_input_auth" name="auth_username" id="auth_username" placeholder="$l_user" required>
                <label for="auth_username" class="auth_input_icon_bg">
                    <div class="auth_input_icon" style="mask: url(./img/noun-user-6714086-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-user-6714086-grey.svg) no-repeat center / contain" ></div>
                </label>
            </div>
            <div class="auth_input_cont" >
                <input type="email" class="win_dark_input win_input_auth" name="auth_email" id="auth_email" placeholder="$l_email" required>
                <label for="auth_email" class="auth_input_icon_bg">
                    <div class="auth_input_icon" style="mask: url(./img/noun-email-842043-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-email-842043-grey.svg) no-repeat center / contain" ></div>
                </label>
            </div>
            <div class="auth_input_cont">
                <input type="password" class="win_dark_input win_input_auth" name="auth_password" id="auth_password" placeholder="$l_pass" required>
                <label for="auth_password" class="auth_input_icon_bg">
                    <div class="auth_input_icon" style="mask: url(./img/noun-password-2891566-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-password-2891566-grey.svg) no-repeat center / contain" ></div>
                </label>
            </div>
        EOT;

        // honeypot field -> hidden from users & screen readers -> only bots will fill this field
        // if honeypot field is not empty -> redirect to previous page
        // is set as not required in JS when form is submitted
        echo <<<EOT
            <div class="auth_input_cont auth_pin" aria-hidden="true">
                <input type="password" class="win_dark_input win_input_auth" name="auth_pin" id="auth_pin" placeholder="PIN" aria-hidden="true" tabindex="-1" required>
                <label for="auth_password" class="auth_input_icon_bg" aria-hidden="true">
                    <div class="auth_input_icon" style="mask: url(./img/noun-password-2891566-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-password-2891566-grey.svg) no-repeat center / contain" aria-hidden="true"></div>
                </label>
            </div>
            EOT;
        echo <<<EOT
        </fieldset>
        <button class="auth_submit_btn" type="submit" onclick="setNotRequired('auth_pin')">
            <p>$l_login</p>
            <div class="auth_input_icon auth_submit_icon" style="mask: url(./img/noun-login-1019092-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-login-1019092-grey.svg) no-repeat center / contain" ></div>
        </button>
        EOT;
    } else {
        $username = htmlspecialchars($_SESSION['name']);

        // collect all lang strings for the auth form
        $l_logout = lang_strings['logout'];
        $l_user_greet = lang_strings['user_greet'];

        echo <<<EOT
        <form class="auth_form" id="auth_window" action="./util/auth_logout.php" method="post" autocomplete="off">
            <div class="auth_greeting">
                <p class="auth_welcome">$l_user_greet</p>
                <div class="auth_user">
                    <p class="auth_user">$username</p>
                    <p class="auth_welcome">!</p>
                </div>
            </div>
            <button class="auth_submit_btn auth_signout_btn" type="submit">
                <p>$l_logout</p>
                <div class="auth_input_icon auth_submit_icon" style = "mask: url(./img/noun-login-1019092-logout-grey.svg) no-repeat center / contain; -webkit-mask: url(./img/noun-login-1019092-logout-grey.svg) no-repeat center / contain" ></div >
            </button>
        EOT;
    }
    echo "</form>";

    if ($lang_title === 'home') {
        echo "<main style='padding: 0 0 6rem 0;'>";
    }
    else {
        echo "<main style='padding: 5rem 0 6rem 0;'>";
    }


    echo '<div class="loading_overlay auth_overlay" id="auth_overlay" style="display: none"></div>';
}
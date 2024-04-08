<?php /** @noinspection CssUnknownTarget */
function template_header($title = "Home"): void
{
    // TODO: support for switching between dark and light mode
    // TODO: support for changing the language

    // ------------------- LOGIN CHECK -------------------
    $loggedIn = false;

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        $loggedIn = true;
    }
    // ----------------- LOGIN CHECK END -------------------

    echo <<<EOT
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$title — LGBTQIA+ & Friends</title>
        <link rel="stylesheet" href="./css/style_common.css">
        <link rel="icon" type="image/png" sizes="32x32" href="../icons/lgbt_bunny.png">
    </head>
    <body>
    <div class="page_wrap">
        <header>
            <div class="logo-header">
                <a href="./index.php">
                    <img class="logo" src="./icons/lgbt_bunny_white.svg" alt="Logo">
                </a>
            </div>
            <button class="auth_button" id="auth_button" onclick="toggleAuthWindow()">
                <div class="auth_icon" id="auth_icon" style="mask: url(./icons/noun-user-6714086-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-user-6714086-grey.svg) no-repeat center / contain" ></div>
            </button>
    </header>
    EOT;

    if (!$loggedIn) {
        echo <<<EOT
        <form class="auth_form" id="auth_window" action="./util/auth_login.php" method="post">
        <fieldset class="auth_fieldset">
            <legend>Sign In</legend>
            <div class="auth_input_cont">
                <input type="text" class="win_dark_input win_input_auth" name="auth_username" id="auth_username" placeholder="Username" required>
                <label for="auth_username" class="auth_input_icon_bg">
                    <div class="auth_input_icon" style="mask: url(./icons/noun-user-6714086-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-user-6714086-grey.svg) no-repeat center / contain" ></div>
                </label>
            </div>
            <div class="auth_input_cont" >
                <input type="email" class="win_dark_input win_input_auth" name="auth_email" id="auth_email" placeholder="Email" required>
                <label for="auth_email" class="auth_input_icon_bg">
                    <div class="auth_input_icon" style="mask: url(./icons/noun-email-842043-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-email-842043-grey.svg) no-repeat center / contain" ></div>
                </label>
            </div>
            <div class="auth_input_cont">
                <input type="password" class="win_dark_input win_input_auth" name="auth_password" id="auth_password" placeholder="Password" required>
                <label for="auth_password" class="auth_input_icon_bg">
                    <div class="auth_input_icon" style="mask: url(./icons/noun-password-2891566-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-password-2891566-grey.svg) no-repeat center / contain" ></div>
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
                    <div class="auth_input_icon" style="mask: url(./icons/noun-password-2891566-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-password-2891566-grey.svg) no-repeat center / contain" aria-hidden="true"></div>
                </label>
            </div>
            EOT;
        echo <<<EOT
        </fieldset>
        <button class="auth_submit_btn" type="submit" onclick="setNotRequired('auth_pin')">
            <p>Sign In</p>
            <div class="auth_input_icon auth_submit_icon" style="mask: url(./icons/noun-login-1019092-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-login-1019092-grey.svg) no-repeat center / contain" ></div>
        </button>
        EOT;
    } else {
        $username = htmlspecialchars($_SESSION['name']);

        echo <<<EOT
        <form class="auth_form" id="auth_window" action="./util/auth_logout.php" method="post" autocomplete="off">
        <div class="auth_greeting">
            <p class="auth_welcome">Welcome back,</p>
            <div class="auth_user">
                <p class="auth_user">{$username}</p>
                <p class="auth_welcome">!</p>
            </div>
        </div>
        <button class="auth_submit_btn auth_signout_btn" type="submit">
            <p>Sign Out</p>
            <div class="auth_input_icon auth_submit_icon" style = "mask: url(./icons/noun-login-1019092-logout-grey.svg) no-repeat center / contain; -webkit-mask: url(./icons/noun-login-1019092-logout-grey.svg) no-repeat center / contain" ></div >
        </button>
        EOT;
    }
    echo <<<EOT
    </form>

    <main>
        <div class="loading_overlay auth_overlay" id="auth_overlay" style="display: none"></div>
    EOT;
}
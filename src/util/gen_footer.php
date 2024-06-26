<?php /** @noinspection CssUnknownTarget */
require_once(__DIR__ . '/validate.php'); // include database connection file

function template_footer($dbConnection, array $scripts = null, bool $loggedIn = false): void
{
    $PDO = $dbConnection->getConnection();

    // add authorization window script to scripts array
    $scripts[] = 'auth_window';

    gen_login_dialog($loggedIn);

    echo <<<EOT
    </main>
    <footer>
        <nav class="nav-bottom navbar">
            <ul class="nav_list">
    EOT;
    echo '<li><a href="./">'.lang_strings['home'].'</a></li>';

    if ($PDO !== null || $dbConnection->checkDBSchema() === true) {
        echo '<li><a href="./calendar">'.lang_strings['cal'].'</a></li>';
    }

    echo '<li><a href="./privacy">'.lang_strings['privacy'].'</a></li>';

    $auth_btn_alt = "aria-label='". lang_strings['account_icon']."'";
    $auth_btn_title = "title='". lang_strings['account_button']."'";

    echo <<<EOT
            </ul>
        </nav>
        <button class="auth_button" id="auth_button" onclick="toggleAuthWindow()" $auth_btn_title>
            <div role="img" $auth_btn_alt class="auth_icon" id="auth_icon" style="mask: url(./img/noun-user-6714086-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-user-6714086-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain"></div>
        </button>
    </footer>
</div>
EOT;
    if ($scripts !== null) {
        foreach ($scripts as $script) {
            echo '<script src="./js/min/' . $script . '.min.js"></script>';
        }
    }
    echo <<<EOT
</body>
</html>
EOT;
    getErrorMsg();
}

function gen_login_dialog(bool $loggedIn = false): void{
    echo '<dialog class="auth_dialog" id="auth_dialog">';

    echo '<div class="loading_overlay auth_overlay" id="auth_dial_overlay"></div>';

    if (!$loggedIn) {
        // collect all lang strings for the auth form
        $l_login = lang_strings['login'];
        $l_user = lang_strings['user'];
        $l_email = lang_strings['email'];
        $l_pass = lang_strings['pass'];

        $user_icon_alt = "aria-label='". lang_strings['user']."'";
        $email_icon_alt = "aria-label='". lang_strings['email']."'";
        $pass_icon_alt = "aria-label='". lang_strings['pass']."'";

        echo <<<EOT
        <form class="auth_form" id="auth_form" action="./util/auth_login" method="post">
        <fieldset class="auth_fieldset">
            <legend>$l_login</legend>
            <div class="auth_input_cont">
                <input type="text" class="win_dark_input win_input_auth" name="auth_username" id="auth_username" placeholder="$l_user" required>
                <label for="auth_username" class="auth_input_icon_bg">
                    <div role="img" $user_icon_alt class="auth_input_icon" style="mask: url(./img/noun-user-6714086-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-user-6714086-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain"></div>
                </label>
            </div>
            <div class="auth_input_cont" >
                <input type="email" class="win_dark_input win_input_auth" name="auth_email" id="auth_email" placeholder="$l_email" required>
                <label for="auth_email" class="auth_input_icon_bg">
                    <div role="img" $email_icon_alt class="auth_input_icon" style="mask: url(./img/noun-email-842043-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-email-842043-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain"></div>
                </label>
            </div>
            <div class="auth_input_cont">
                <input type="password" class="win_dark_input win_input_auth" name="auth_password" id="auth_password" placeholder="$l_pass" required>
                <label for="auth_password" class="auth_input_icon_bg">
                    <div role="img" $pass_icon_alt class="auth_input_icon" style="mask: url(./img/noun-password-2891566-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-password-2891566-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain"></div>
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
                    <div class="auth_input_icon" style="mask: url(./img/noun-password-2891566-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-password-2891566-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain" aria-hidden="true"></div>
                </label>
            </div>
            EOT;
        echo <<<EOT
        </fieldset>
        <button class="auth_submit_btn" type="submit" onclick="setNotRequired('auth_pin')">
            <p>$l_login</p>
            <div class="auth_input_icon auth_submit_icon" style="mask: url(./img/noun-login-1019092-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-login-1019092-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain"></div>
        </button>
        EOT;
    } else {
        $username = htmlspecialchars($_SESSION['name']);

        // collect all lang strings for the auth form
        $l_logout = lang_strings['logout'];
        $l_user_greet = lang_strings['user_greet'];

        echo <<<EOT
        <form class="auth_form" id="auth_form" action="./util/auth_logout" method="post" autocomplete="off">
            <div class="auth_greeting">
                <p class="auth_welcome">$l_user_greet</p>
                <div class="auth_user">
                    <p class="auth_user">$username</p>
                    <p class="auth_welcome">!</p>
                </div>
            </div>
            <button class="auth_submit_btn auth_signout_btn" type="submit">
                <p>$l_logout</p>
                <div class="auth_input_icon auth_submit_icon" style = "mask: url(./img/noun-login-1019092-logout-grey.svg) no-repeat center / contain; -webkit-mask-image: url(./img/noun-login-1019092-logout-grey.svg); -webkit-mask-repeat:  no-repeat; -webkit-mask-position:  center; -webkit-mask-size: contain"></div >
            </button>
        EOT;
    }
    echo "</form>";
    echo "</dialog>";
}
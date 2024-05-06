<?php

require_once(dirname(__DIR__) . '/util/auth_session_start.php'); // session start functions

$_SESSION = array(); // clear session data

// expire session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'], 2)); // redirect to home page
exit();

<?php
require_once(__DIR__ . '/conn_db.php'); // include database connection file
require_once(__DIR__ . '/validate.php'); // validate functions
require_once(__DIR__ . '/utils.php'); // include utility functions
require_once(__DIR__ . '/conf.php'); // include configuration file

$dbConnection = new DBConnection();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirect(); // redirect to home page
}

require_once(__DIR__ . '/auth_session_start.php'); // session start functions

$username = (string)$_POST['auth_username']; // store username
$email = (string)$_POST['auth_email']; // store email
$password = (string)$_POST['auth_password']; // store password in plaintext

$honeypot = $_POST['auth_pin']; // store honeypot value

if (!empty($honeypot)) {
    redirectToPreviousPage("333"); // honeypot value not empty -> redirect to previous page
} else {
    unset($honeypot); // honeypot value empty -> unset honeypot value
    // echo "honeypot value empty";
}

validate_login($username, $email, $password); // validate login data

$stmt = $PDO->prepare("SELECT * FROM accounts WHERE username = :username AND email = :email"); // prepare statement to select account data
$stmt->bindParam(':username', $username); // bind parameter :username to $_POST['username']
$stmt->bindParam(':email', $email); // bind parameter :email to $_POST['email']
$stmt->execute(); // execute statement
$account = $stmt->fetch(PDO::FETCH_ASSOC); // fetch account data and store in $account

if ($stmt->rowCount() > 0) {
    $pw_hash = $account['password']; // store password hash
    $id = $account['id']; // store account id

    if (password_verify($password, $pw_hash)) { // compare plaintext password with password hash
        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $username;
        $_SESSION['id'] = $id;
    } else {
        // incorrect password
        redirectToPreviousPage("333"); // "Incorrect password/username/email" -> don't tell the user which one is incorrect
    }
} else {
    // incorrect username or email
    redirectToPreviousPage("333");
}

redirect(); // redirect to home page

/*
 * the following php.ini settings are recommended for session security:
 * session.cookie_httponly = 1
 * session.use_only_cookies = 1
 * session.cookie_secure = 1
 */
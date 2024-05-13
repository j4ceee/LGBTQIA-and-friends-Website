<?php
require_once(__DIR__ . '/utils.php'); // include utility functions

// ------------------- LOGIN CHECK -------------------

require_once(__DIR__ . '/auth_session_start.php'); // start session

session_regenerate_id(true); // regenerate session ID to prevent session fixation attacks

require_once(__DIR__ . '/auth_login_check.php'); // check if user is logged in
/* @var bool $loggedIn */

/*
if (!$loggedIn) {
    redirectError("/", 334);
}
*/

// ----------------- LOGIN CHECK END -------------------

// ------------------- DATABASE CONNECTION -------------------

require_once(__DIR__ . '/conn_db.php'); // include database connection file

$dbConnection = new DBConnection();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectError("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

require_once(__DIR__ . '/gen_ics.php');

$ICSGen = new ICSGenerator();
$ICSGen->generateICS();
redirect();

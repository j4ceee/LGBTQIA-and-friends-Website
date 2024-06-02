<?php
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file

if (ENV === "dev") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ------------------- LOGIN CHECK -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // start session

session_regenerate_id(true); // regenerate session ID to prevent session fixation attacks

require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var $loggedIn */

/* // TODO: enable login system
if (!$loggedIn) {
    redirectError("/", 334);
}
*/

// ----------------- LOGIN CHECK END -------------------

// ------------------- DATABASE CONNECTION -------------------

require_once(__DIR__ . '/util/conn_db.php'); // include database connection file

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectStatus("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

if (isset($_GET['id'])) {

    // sanitize eventID
    $eventID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($eventID !== false) {
        // prepare SQL to delete the event entry
        $stmt = $PDO->prepare('DELETE FROM events WHERE id = :eventID');
        $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
        $stmt->execute();

        $msg = '200/You have deleted the Event with id: ' . $eventID;
    }
}
$msg = '404/Event id';

redirectStatus("/calendar.php", $msg);

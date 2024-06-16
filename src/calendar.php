<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file
require_once __DIR__ . '/util/gen_calendar.php';

if (ENV === "dev") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ------------------- DATABASE CONNECTION -------------------

require_once(__DIR__ . '/util/conn_db.php'); // include database connection file

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectStatus("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // include language file
require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var $loggedIn */

require_once(__DIR__ . '/util/lang_get.php'); // get language
/* @var string $lang */

template_header($dbConnection, $lang, 'cal');
?>

<div class="page_content">
    <?php
    $event_id = null;

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $event_id = $_GET['id'];
    }

    if (ENV === "dev" || $loggedIn) {
        $cal_admin = true;
    } else {
        $cal_admin = false;
    }

    gen_calendar($lang, 1, "full", $cal_admin, $event_id);
    ?>
</div>

<?php
template_footer($dbConnection, ["view_calendar"], $loggedIn);
?>

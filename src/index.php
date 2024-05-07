<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/conn_db.php'); // include database connection file
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file

$dbConnection = new DBConnection();
$PDO = $dbConnection->getConnection();

require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var bool $loggedIn */

template_header($dbConnection,'Home');
?>

<div class="welcome_slide">
    <div class="welcome_slide_content">
        <h1 class="heading_start"><span class="heading_top">LGBTQIA+ & Friends</span>
            <span class="heading_btm">Hochschule Ansbach</span></h1>
        <img class="heading_logo" src="./img/lgbt_bunny.svg" alt="Logo">
    </div>
</div>


<?php
template_footer();
?>


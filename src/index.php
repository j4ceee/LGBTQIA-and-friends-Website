<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file
require_once(__DIR__ . '/util/gen_calendar.php');

if (ENV === "dev") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ------------------- DATABASE CONNECTION -------------------

require_once(__DIR__ . '/util/conn_db.php'); // include database connection file

$dbConnection = new DBConnection();
$PDO = $dbConnection->useDB();

// ----------------- DATABASE CONNECTION END -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // include language file
require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var bool $loggedIn */

require_once(__DIR__ . '/util/get_lang.php'); // get language
/* @var string $lang */

template_header($dbConnection, $lang, 'home');
?>

<div class="welcome_slide">
    <div class="welcome_slide_content">
        <h1 class="heading_start"><span class="heading_top"><?php echo lang_strings['title'] ?></span>
            <span class="heading_btm"><?php echo lang_strings['uni'] ?></span></h1>
        <img class="heading_logo" src="./img/lgbt_bunny.svg" alt="Logo">
    </div>
</div>

<div class="page_content">
    <section class="admin">
        <div class="section_header">
            <h2 class="section_heading"><?php echo lang_strings['admin'] ?></h2>
            <div class="section_header_underline"></div>
        </div>
        <a href="./event_manage.php">Add Event</a>
        <a href="./util/refresh_ics.php">Refresh ICS files</a>
    </section>

    <section class="about">
        <div class="section_header">
            <h2 class="section_heading"><?php echo lang_strings['about'] ?></h2>
            <div class="section_header_underline"></div>
        </div>
        <div class="about_txt_container">
            <p class="about_text">
                <?php echo lang_strings['about_text'] ?>
            </p>
        </div>
    </section>

    <?php
    gen_calendar($lang, 2);
    ?>

</div>

<?php
template_footer(["view_calendar.js"]);
?>


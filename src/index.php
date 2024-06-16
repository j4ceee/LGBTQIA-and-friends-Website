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

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

// ----------------- DATABASE CONNECTION END -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // start session
require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var $loggedIn */

require_once(__DIR__ . '/util/lang_get.php'); // get language
/* @var string $lang */

template_header($dbConnection, $lang, 'home');
?>

<div class="welcome_slide">
    <canvas id="canvas" style="width: 100%; height: 100%; padding: 0; margin: 0;"></canvas>
    <div class="welcome_slide_content">
        <h1 class="heading_start"><span class="heading_top"><?php echo lang_strings['title'] ?></span>
            <span class="heading_btm"><?php echo lang_strings['uni'] ?></span></h1>
        <img id="canvas_light" class="heading_logo" src="./img/lgbt_bunny_opt.svg" alt="<?php echo lang_strings['alt_signet'] ?>">
    </div>
</div>

<div class="page_content">
    <?php if (($PDO !== null && (!$dbConnection->checkDBExists() || $dbConnection->checkDBSchema() !== true)) || ENV === "dev" || $loggedIn) :
        // only show admin section if the database is not set up or the user is logged in or the environment is set to "dev"
        ?>
    <section class="admin">
        <div class="section_header">
            <h2 class="section_heading"><?php echo lang_strings['admin'] ?></h2>
            <div class="section_header_underline"></div>
        </div>
        <div class="admin_controls">

            <?php if ($PDO !== null && (!$dbConnection->checkDBExists() || $dbConnection->checkDBSchema() !== true)) : // always show setup db button if the database is not set up ?>
            <a href="./util/setup_db.php" class="lgbt_button">Setup DB</a>
            <?php endif; ?>

            <?php if (ENV === "dev" || $loggedIn) : // only show main admin tools if env is set to "dev" or the user is logged in ?>
            <a href="./event_manage.php" class="lgbt_button">Add Event</a>
            <a href="./util/refresh_ics.php" class="lgbt_button">Refresh ICS files</a>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

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
    gen_calendar($lang, 2, "compact");
    ?>

</div>

<?php
template_footer($dbConnection, ["view_calendar", "animated_bg"], $loggedIn);
?>


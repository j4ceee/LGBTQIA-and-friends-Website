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

<div id="canvas_light" style="position: fixed; top: 50%; left: 50%; width: 0; height: 0;"></div>

<div class="welcome_slide">
    <canvas id="canvas" class="canvas_anim"></canvas>
    <div class="welcome_slide_content">
        <h1 class="heading_start"><span class="heading_top"><?php echo lang_strings['title'] ?></span>
            <span class="heading_btm"><?php echo lang_strings['uni'] ?></span></h1>
        <img class="heading_logo" src="./img/lgbt_bunny_opt.svg" alt="<?php echo lang_strings['alt_signet'] ?>">
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
            <a href="./util/setup_db" class="lgbt_button">Setup DB</a>
            <?php endif; ?>

            <?php if (ENV === "dev" || $loggedIn) : // only show main admin tools if env is set to "dev" or the user is logged in ?>
            <a href="./event_manage" class="lgbt_button">Add Event</a>
            <a href="./util/refresh_ics" class="lgbt_button">Refresh ICS files</a>
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
    // TODO: notice that registration is required via socials / email to attend events
    ?>
</div>
<div class="balls_slide_2">
    <canvas id="canvas2" class="canvas_anim"></canvas>
    <section class="page_content team">
        <div class="section_header">
            <h2 class="section_heading"><?php echo lang_strings['team'] ?></h2>
            <div class="section_header_underline"></div>
        </div>

        <div class="staff_presentation">
            <?php
            $staffList = [
                "linda" => [
                    "name" => "Linda",
                    "pronouns" => lang_strings['staff_linda_pronouns'],
                    "desc" => lang_strings['staff_linda_desc'],
                    "img" => null,
                    "img_note" => null,
                    "url" => null,
                ],
                "juno" => [
                    "name" => "Juno",
                    "pronouns" => lang_strings['staff_juno_pronouns'],
                    "desc" => lang_strings['staff_juno_desc'],
                    "img" => null,
                    "img_note" => null,
                    "url" => null,
                ],
                "cedric" => [
                    "name" => "Cedric",
                    "pronouns" => lang_strings['staff_cedric_pronouns'],
                    "desc" => lang_strings['staff_cedric_desc'],
                    "img" => "cedric.jpg",
                    "img_note" => lang_strings['staff_img_by'] . " <a href='https://www.artstation.com/artwork/9Y3gR'>Misha Niklash</a>",
                    "url" => "https://github.com/j4ceee",
                ],
            ];

            foreach ($staffList as $staff) {
                echo<<<EOT
        <div class='staff_card'>
            <div class='staff_img_container'>
                <div class='staff_img_clip'>
        EOT;

                if ($staff["img"] !== null) {
                    if ($staff["url"] !== null) {
                            echo "<a class='staff_url' href='$staff[url]'>";
                    }

                    $staff_alt = 'staff_'.strtolower($staff['name']).'_img_alt';
                    if (lang_strings[$staff_alt] !== null) {
                        $staff_alt = lang_strings['staff_img_alt'] . " $staff[name], " . lang_strings[$staff_alt];
                    } else {
                        $staff_alt = lang_strings['staff_img_alt'] . " $staff[name]";
                    }

                    echo "<img class='staff_img' src='./img/" . $staff["img"] . "' alt='$staff_alt'>";
                    if ($staff["url"] !== null) {
                        echo "</a>";
                    }
                }

                echo<<<EOT
                </div>
                <div class='staff_img_note'>$staff[img_note]</div>
            </div>
            <div class='staff_info'>
                <p class='staff_info_field staff_name'><strong>$staff[name]</strong></p>
                <p class='staff_info_field staff_pronouns'>($staff[pronouns])</p>
                <p class='staff_info_field staff_desc'>$staff[desc]</p>
            </div>
        </div>
        EOT;
            }
            ?>
        </div>
    </section>
</div>
<div class="page_content">
    <?php require_once(__DIR__ . '/util/get_socials.php') ?>
</div>

<?php
template_footer($dbConnection, ["view_calendar", "animated_bg"], $loggedIn);
?>


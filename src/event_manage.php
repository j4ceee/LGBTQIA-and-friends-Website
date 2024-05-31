<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file

if (ENV === "dev") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ------------------- LOGIN CHECK -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // start session

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
    redirectError("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

// ---------------------- EDIT MODE ----------------------------

//TODO: implement edit mode

// ------------------- EDIT MODE END ---------------------------

require_once(__DIR__ . '/util/lang_get.php'); // get language
/* @var string $lang */



template_header($dbConnection, $lang, 'event_add');
?>

    <form class="manage_event_form" action="./util/validate_add.php" method="post" autocomplete="off">
        <fieldset class="event_general">
            <legend>Event Allgemein</legend>

            <div class="event_time">
                <div class="event_detail">
                    <label for="event_date_start">Beginn<abbr class="form_req_marking">*</abbr></label>
                    <input type="datetime-local" class="lgbt_input event_date_start" id="event_date_start" name="event_date_start" required>
                </div>

                <span class="event_detail event_dur">—</span>

                <div class="event_detail">
                    <label for="event_date_end">Ende<abbr class="form_req_marking">*</abbr></label>
                    <input type="datetime-local" class="lgbt_input event_date_end" id="event_date_end" name="event_date_end" required>
                </div>
            </div>

            <div class="event_detail event_detail_location">
                <label for="event_location">Ort<abbr class="form_req_marking">*</abbr></label>
                <input type="text" class="lgbt_input event_location" id="event_location" name="event_location" placeholder="Ort" required list="event_location_list">
                <datalist id="event_location_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name FROM event_locations ORDER BY name');
                    $stmt->execute();
                    $event_locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($event_locations); $i++) {
                        echo '<option value="' . $event_locations[$i]['name'] . '">';
                    }
                    ?>
                </datalist>
            </div>

            <div>
                <label for="enable_desc">Beschreibung verwenden</label>
                <input type="checkbox" class="lgbt_input enable_desc" id="enable_desc" name="enable_desc">
            </div>
        </fieldset>

        <div class="event_locales">
            <fieldset class="event_info_de event_info">
                <legend>Event Info Deutsch</legend>

                <label for="event_name_de">Titel<abbr class="form_req_marking">*</abbr></label>
                <input type="text" class="lgbt_input event_name_de" id="event_name_de" name="event_name_de" placeholder="Titel" required list="event_name_de_list" oninput="setOtherTitle('de')">
                <datalist id="event_name_de_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name_de FROM event_types ORDER BY id');
                    $stmt->execute();
                    $event_type_de = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($event_type_de); $i++) {
                        echo '<option data-value="'. $i .'" value="' . $event_type_de[$i]['name_de'] . '">';
                    }
                    ?>
                </datalist>

                <label for="event_desc_de">Beschreibung</label>
                <textarea class="lgbt_input event_desc_de" id="event_desc_de" name="event_desc_de" placeholder="Beschreibung" disabled></textarea>
            </fieldset>

            <fieldset class="event_info event_info_en">
                <legend>Event Info English</legend>

                <label for="event_name_en">Title<abbr class="form_req_marking">*</abbr></label>
                <input type="text" class="lgbt_input event_name_en" id="event_name_en" name="event_name_en" placeholder="Title" required list="event_name_en_list" oninput="setOtherTitle('en')">
                <datalist id="event_name_en_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name_en FROM event_types ORDER BY id');
                    $stmt->execute();
                    $event_type_en = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($event_type_en); $i++) {
                        echo '<option data-value="'. $i .'" value="' . $event_type_en[$i]['name_en'] . '">';
                    }
                    ?>
                </datalist>

                <label for="event_desc_en">Description</label>
                <textarea class="lgbt_input event_desc_en" id="event_desc_en" name="event_desc_en" placeholder="Description" disabled></textarea>
            </fieldset>
        </div>

        <input type="submit" class="lgbt_button" value="Event erstellen">
    </form>



<?php
template_footer($dbConnection, ["manage_event.js"], $loggedIn);
?>
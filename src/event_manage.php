<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/conn_db.php'); // include database connection file
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file

$dbConnection = new DBConnection();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirect();
}

require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var bool $loggedIn */

template_header($dbConnection,'Manage Event');
?>

    <form class="manage_event_form" autocomplete="off">
        <fieldset class="event_general">
            <legend>Event Allgemein</legend>

            <div class="event_time">
                <div class="event_detail">
                    <label for="event_date_start">Beginn<span class="form_req_marking">*</span></label>
                    <input type="datetime-local" class="lgbt_input event_date_start" id="event_date_start" name="event_date_start" required>
                </div>

                <span class="event_detail event_dur">—</span>

                <div class="event_detail">
                    <label for="event_date_end">Ende<span class="form_req_marking">*</span></label>
                    <input type="datetime-local" class="lgbt_input event_date_end" id="event_date_end" name="event_date_end" required>
                </div>
            </div>

            <div class="event_detail event_detail_location">
                <label for="event_location">Ort<span class="form_req_marking">*</span></label>
                <input type="text" class="lgbt_input event_location" id="event_location" name="event_location" placeholder="Ort" required>
            </div>

            <div>
                <label for="enable_desc">Beschreibung verwenden</label>
                <input type="checkbox" class="lgbt_input enable_desc" id="enable_desc" name="enable_desc">
            </div>
        </fieldset>

        <div class="event_locales">
            <fieldset class="event_info_de event_info">
                <legend>Event Info Deutsch</legend>

                <label for="event_name_de">Titel<span class="form_req_marking">*</span></label>
                <input type="text" class="lgbt_input event_name_de" id="event_name_de" name="event_name_de" placeholder="Titel" required list="event_name_de_list" oninput="setOtherTitle('de')">
                <datalist id="event_name_de_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name_de FROM event_types ORDER BY id ASC');
                    $stmt->execute();
                    $event_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($event_types); $i++) {
                        echo '<option data-value="'. $i .'" value="' . $event_types[$i]['name_de'] . '">';
                    }
                    ?>
                </datalist>

                <label for="event_desc_de">Beschreibung</label>
                <textarea class="lgbt_input event_desc_de" id="event_desc_de" name="event_desc_de" placeholder="Beschreibung" disabled></textarea>
            </fieldset>

            <fieldset class="event_info event_info_en">
                <legend>Event Info English</legend>

                <label for="event_name_en">Title<span class="form_req_marking">*</span></label>
                <input type="text" class="lgbt_input event_name_en" id="event_name_en" name="event_name_en" placeholder="Title" required list="event_name_en_list" oninput="setOtherTitle('en')">
                <datalist id="event_name_en_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name_en FROM event_types ORDER BY id ASC');
                    $stmt->execute();
                    $event_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($event_types); $i++) {
                        echo '<option data-value="'. $i .'" value="' . $event_types[$i]['name_en'] . '">';
                    }
                    ?>
                </datalist>

                <label for="event_desc_en">Description</label>
                <textarea class="lgbt_input event_desc_en" id="event_desc_en" name="event_desc_en" placeholder="Description" disabled></textarea>
            </fieldset>
        </div>

        <a href="#" class="event_submit">Submit</a>
    </form>



<?php
template_footer(["manage_event.js"]);
?>
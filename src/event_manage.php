<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file
require_once(__DIR__ . '/util/validate.php'); // include validation functions

if (ENV === "dev") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ------------------- LOGIN CHECK -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // start session

require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var $loggedIn */

if (!$loggedIn && ENV !== "dev") {
    redirectStatus("/", 334);
}

// ----------------- LOGIN CHECK END -------------------

// ------------------- DATABASE CONNECTION -------------------

require_once(__DIR__ . '/util/conn_db.php'); // include database connection file

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectStatus("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

// ---------------------- EDIT MODE ----------------------------

$eventID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : null;

$editMode = false;
$event = null;

if ($eventID !== null) {
    $sql = "
        SELECT
            e.id as eventID,
            e.date_start,
            e.date_end,
            el.name as location,
            et.name_de as name_de,
            et.name_en as name_en,
            et.desc_de as desc_de,
            et.desc_en as desc_en,
            e.desc_de_override as desc_de_override,
            e.desc_en_override as desc_en_override
        FROM
            events as e
        JOIN
            event_types as et
        ON
            e.event_type_id = et.id
        JOIN
            event_locations as el
        ON
            e.event_location_id = el.id
        WHERE
            e.id = :eventID";

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event !== false && count($event) > 0 && $event !== null) {
        $editMode = true;
    }
    else {
        redirectStatus("/calendar.php", "601/" . $eventID);
    }
}


// ------------------- EDIT MODE END ---------------------------

require_once(__DIR__ . '/util/lang_get.php'); // get language
/* @var string $lang */

if ($editMode) {
    $pageTitle = "event_edit_p";
}
else {
    $pageTitle = "event_add";
}
template_header($dbConnection, $lang, $pageTitle);
?>

<?php

if ($editMode) {
    $formAction = "./util/validate_update?id=" . $eventID;
}
else {
    $formAction = "./util/validate_add";
}
?>
<div class="page_content manage_event_content">
    <form class="manage_event_form" action="<?php echo $formAction ?>" method="post" autocomplete="off">
        <fieldset class="event_general">
            <legend><?php echo lang_strings['event_info_general']?></legend>

            <div class="event_time">
                <div class="event_detail">
                    <label for="event_date_start"><?php echo lang_strings['start']?><abbr class="form_req_marking">*</abbr></label>
                    <input type="datetime-local" class="lgbt_input event_date_start" id="event_date_start" name="event_date_start"
                           <?php
                            if ($editMode) {
                                 echo 'value="' . convert_datetime_to_ui($event['date_start']) . '"';
                            }
                            ?>
                           required>
                </div>

                <span class="event_detail event_dur">—</span>

                <div class="event_detail">
                    <label for="event_date_end"><?php echo lang_strings['end']?><abbr class="form_req_marking">*</abbr></label>
                    <input type="datetime-local" class="lgbt_input event_date_end" id="event_date_end" name="event_date_end"
                            <?php
                            if ($editMode) {
                                echo 'value="' . convert_datetime_to_ui($event['date_end']) . '"';
                            }
                            ?>
                           required>
                </div>
            </div>

            <div class="event_detail event_detail_location">
                <label for="event_location"><?php echo lang_strings['location']?><abbr class="form_req_marking">*</abbr></label>
                <input type="text" class="lgbt_input event_location" id="event_location" name="event_location" placeholder="Ort" required list="event_location_list"
                    <?php
                    if ($editMode) {
                        echo 'value="' . htmlspecialchars($event['location']) . '"';
                    }
                    ?>
                >
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

            <label class="win_dark_check_label" for="enable_desc"><span><?php echo lang_strings['use_desc']?></span>
                <?php
                $descEnabled = "";

                if ($editMode) {
                    // if override is "-" (no description), disable the checkbox
                    // if override is not "-" and there is a default description, enable the checkbox
                    if ($event['desc_de_override'] !== "-" && $event['desc_en_override'] !== "-" && $event['desc_de'] !== "" && $event['desc_en'] !== "") {
                        $descEnabled = "checked";
                    }
                }
                ?>

                <input type="checkbox" class="enable_desc win_dark_check_org" id="enable_desc" name="enable_desc" <?php echo $descEnabled ?>>
                <span class="win_dark_check"></span>
            </label>
        </fieldset>

        <div class="event_locales">
            <fieldset class="event_info_de event_info">
                <legend>Event Info Deutsch</legend>

                <label for="event_name_de">Titel<abbr class="form_req_marking">*</abbr></label>
                <input type="text" class="lgbt_input event_name_de" id="event_name_de" name="event_name_de" placeholder="Titel" required list="event_name_de_list" oninput="setOtherTitle('de')"
                    <?php
                    if ($editMode) {
                        echo 'value="' . htmlspecialchars($event['name_de']) . '"';
                    }
                    ?>
                >
                <datalist id="event_name_de_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name_de, id FROM event_types ORDER BY name_de');
                    $stmt->execute();
                    $event_type_en = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($event_type_en as $key => $event_type) {
                        echo '<option data-value="'. (int)$event_type['id'] .'" value="' . htmlspecialchars($event_type['name_de']) . '">';
                    }
                    ?>
                </datalist>

                <label for="event_desc_de">Beschreibung</label>
                <?php
                $descTextarea = null;
                $enableTextArea = "disabled";
                $descPlaceholder = "Beschreibung";

                if ($editMode && $descEnabled === "checked") {
                    $enableTextArea = "";
                    // at this point: override is not "-" (meaning desciption is enabled) and there is a default description
                    // so: we need to check if there is an override description (not null or empty)
                    if ($event['desc_de_override'] !== null && $event['desc_de_override'] !== "") {
                        $descTextarea = htmlspecialchars($event['desc_de_override']);
                    } else if ($event['desc_de'] !== "" && $event['desc_de'] !== null) {
                        // if there is no override description, but the default description is not empty, enable the textarea & set the default description as placeholder
                        $descPlaceholder = "Standard Beschreibung: \n" . htmlspecialchars($event['desc_de']);
                    }
                }
                ?>
                <textarea class="lgbt_input event_desc_de" id="event_desc_de" name="event_desc_de" placeholder="<?php echo $descPlaceholder?>" <?php echo $enableTextArea?>><?php echo $descTextarea ?></textarea>
            </fieldset>

            <fieldset class="event_info event_info_en">
                <legend>Event Info English</legend>

                <label for="event_name_en">Title<abbr class="form_req_marking">*</abbr></label>
                <input type="text" class="lgbt_input event_name_en" id="event_name_en" name="event_name_en" placeholder="Title" required list="event_name_en_list" oninput="setOtherTitle('en')"
                    <?php
                    if ($editMode) {
                        echo 'value="' . htmlspecialchars($event['name_en']) . '"';
                    }
                    ?>
                >
                <datalist id="event_name_en_list">
                    <?php
                    $stmt = $PDO->prepare('SELECT name_en, id FROM event_types ORDER BY name_en');
                    $stmt->execute();
                    $event_type_en = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($event_type_en as $key => $event_type) {
                        echo '<option data-value="'. (int)$event_type['id'] .'" value="' . htmlspecialchars($event_type['name_en']) . '">';
                    }
                    ?>
                </datalist>

                <label for="event_desc_en">Description</label>
                <?php
                $descTextarea = null;
                $enableTextArea = "disabled";
                $descPlaceholder = "Description";

                if ($editMode && $descEnabled === "checked") {
                    $enableTextArea = "";
                    // at this point: override is not "-" (meaning desciption is enabled) and there is a default description
                    // so: we need to check if there is an override description (not null or empty)
                    if ($event['desc_en_override'] !== null && $event['desc_en_override'] !== "") {
                        $descTextarea = htmlspecialchars($event['desc_en_override']);
                    } else if ($event['desc_en'] !== "" && $event['desc_en'] !== null) {
                        // if there is no override description, but the default description is not empty, enable the textarea & set the default description as placeholder
                        $descPlaceholder = "Default Description: \n" . htmlspecialchars($event['desc_en']);
                    }
                }
                ?>
                <textarea class="lgbt_input event_desc_en" id="event_desc_en" name="event_desc_en" placeholder="<?php echo $descPlaceholder?>" <?php echo $enableTextArea?>><?php echo $descTextarea ?></textarea>
            </fieldset>
        </div>

        <?php
        $submitValue = lang_strings['event_add'];
        if ($editMode) {
            $submitValue = lang_strings['event_edit_p'];
        }
        ?>
        <div class="form_buttons">
            <input type="submit" class="lgbt_button" value="<?php echo $submitValue?>">
        </div>
    </form>
</div>



<?php
template_footer($dbConnection, ["manage_event"], $loggedIn);
?>
<?php
require_once(__DIR__ . '/conn_db.php'); // include database connection file
require_once(__DIR__ . '/utils.php'); // include utility functions
require_once(__DIR__ . '/conf.php'); // include configuration file
require_once(__DIR__ . '/validate.php');
require_once(__DIR__ . '/crud_add.php');
require_once(__DIR__ . '/gen_ics.php');

// ------------------- LOGIN CHECK -------------------

require_once(__DIR__ . '/auth_session_start.php'); // start session

session_regenerate_id(true); // regenerate session ID to prevent session fixation attacks

require_once(__DIR__ . '/auth_login_check.php'); // check if user is logged in
/* @var $loggedIn */

/* // TODO: enable login system
if (!$loggedIn) {
    redirectError("/", 334);
}
*/

// ----------------- LOGIN CHECK END -------------------

// ------------------- DATABASE CONNECTION -------------------

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectStatus("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['id']) && is_numeric($_GET['id'])) {

    validate_event_details($_POST);

    $eventID = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // get current event details
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
            e.desc_en_override as desc_en_override,
            e.sequence,
            e.date_modified
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

    echo "<pre>";
    var_dump($event);
    echo "</pre>";

    if ($event !== false && count($event) > 0 && $event !== null) {

        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";

        // sanitize the submitted data
        $event_name_de = trim($_POST['event_name_de']);
        $event_name_en = trim($_POST['event_name_en']);

        $event_location = trim($_POST['event_location']);

        $event_date_start = trim($_POST['event_date_start']);
        $event_date_end = trim($_POST['event_date_end']);
        $event_date_start = convert_datetime_to_db($event_date_start);
        $event_date_end = convert_datetime_to_db($event_date_end);

        if ($event_date_start === null || $event_date_end === null) {
            redirectToPreviousPage("400");
        }

        $event_desc_override = prepare_event_desc_override($_POST);
        $event_desc_de_override = $event_desc_override[0];
        $event_desc_en_override = $event_desc_override[1];

        // compare the submitted data with the current event details
        $compArray = [
            'name_de' => $event_name_de,
            'name_en' => $event_name_en,
            'location' => $event_location,
            'date_start' => $event_date_start,
            'date_end' => $event_date_end,
            'desc_de_override' => $event_desc_de_override,
            'desc_en_override' => $event_desc_en_override
        ];

        $updateArray = [];

        foreach ($compArray as $key => $value) {
            if ($event[$key] !== $value) {
                $updateArray[$key] = $value;

                echo "## New value for " . $key . " ##<br>";
                echo "Old value: " . $event[$key] . "<br>";
                echo "New value: " . $value . "<br>";
                echo "<br>";
            }
        }

        echo "<pre>";
        var_dump($updateArray);
        echo "</pre>";

        // if there are no changes
        if (count($updateArray) === 0) {
            redirectToPreviousPage("504");
        }

        // update the event details
        /*
         * can be updated without further checks:
         *
         * - date_start & date_end
         * - desc_de_override & desc_en_override
         *
         * - sequence (increment by 1)
         * - date_modified (current UTC time)
         *
         * check if already exist: -> if yes, update foreign key to existing entry -> if no, add new entry
         * - name_de & name_en
         * - location
         */

        /**
         ** DB TRANSACTION START
         **/

        $PDO->beginTransaction();

        $columnUpdates = [];

        /** @noinspection SqlWithoutWhere */
        $sql = "UPDATE events SET ";

        // handle event type-----------------------------------------------------------

        if (isset($updateArray['name_de']) && isset($updateArray['name_en'])) {
            $event_type_id = validate_existing_event_type($event_name_de, $event_name_en);

            if ($event_type_id === null) { // if event type does not exist, add it
                $event_type_id = add_event_type($event_name_de, $event_name_en); // add event type into database
            }
            $event_type_id = (int)$event_type_id;

            $sql .= "event_type_id = :event_type_id, ";
            $columnUpdates["event_type_id"] = $event_type_id;
        }
        // event_type_id is now either the id of an existing event type or a new one

        // handle event location-----------------------------------------------------------

        if (isset($updateArray['location'])) {
            $event_location_id = get_existing_event_loc($event_location);

            if ($event_location_id === null) {
                $event_location_id = add_event_loc($event_location); // add event location into database
            }
            $event_location_id = (int)$event_location_id;

            $sql .= "event_location_id = :event_location_id, ";
            $columnUpdates["event_location_id"] = $event_location_id;
        }

        // update the event details -----------------------------------------------------------

        if (isset($updateArray['date_start'])) {
            $sql .= "date_start = :date_start, ";
            $columnUpdates["date_start"] = $updateArray['date_start'];
        }
        if (isset($updateArray['date_end'])) {
            $sql .= "date_end = :date_end, ";
            $columnUpdates["date_end"] = $updateArray['date_end'];
        }
        if (isset($updateArray['desc_de_override'])) {
            $sql .= "desc_de_override = :desc_de_override, ";
            $columnUpdates["desc_de_override"] = $updateArray['desc_de_override'];
        }
        if (isset($updateArray['desc_en_override'])) {
            $sql .= "desc_en_override = :desc_en_override, ";
            $columnUpdates["desc_en_override"] = $updateArray['desc_en_override'];
        }

        // rollback if no actual updates were made
        if (count($columnUpdates) === 0) {
            $PDO->rollBack();
            redirectToPreviousPage("504");
        }

        $sql .= "sequence = :sequence, ";
        $sql .= "date_modified = :date_modified ";
        $sql .= "WHERE id = :eventID";

        echo "<pre>";
        var_dump($sql);
        echo "</pre>";

        $sequence = (int)$event['sequence'] + 1; // increment sequence number by 1
        try {
            $date_modified = new DateTime('now', new DateTimeZone('UTC'));
        } catch (Exception $e) {
            $PDO->rollBack();
            redirectToPreviousPage("400");
        }
        $date_modified = $date_modified->format('Y-m-d H:i:s');

        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':eventID', $eventID, PDO::PARAM_INT);
        $stmt->bindParam(':sequence', $sequence, PDO::PARAM_INT);
        $stmt->bindParam(':date_modified', $date_modified);

        echo "<pre>";
        var_dump($columnUpdates);
        echo "</pre>";

        foreach ($columnUpdates as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();

        //$PDO->rollBack();
        $PDO->commit();

        // generate ICS file
        $ICSGen = new ICSGenerator();
        $ICSGen->generateICS();
    }
}
//redirectStatus("/", "401");

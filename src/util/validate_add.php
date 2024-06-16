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

if (!$loggedIn && ENV !== "dev") {
    redirectStatus("/", 334);
}

// ----------------- LOGIN CHECK END -------------------

// ------------------- DATABASE CONNECTION -------------------

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectStatus("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validate_event_details($_POST);

    $PDO->beginTransaction();

    // handle event name-----------------------------------------------------------

    $event_name_de = trim($_POST['event_name_de']);
    $event_name_en = trim($_POST['event_name_en']);

    // if event name already exists, get the ID
    // if event name does not exist, add it (but both names must be unique)

    $event_type_id = validate_existing_event_type($event_name_de, $event_name_en);

    if ($event_type_id === null) {
        $event_type_id = add_event_type($event_name_de, $event_name_en); // add event type into database
    }


    // handle event location-----------------------------------------------------------

    $event_location = trim($_POST['event_location']);   // string, max 100 chars

    // if event location already exists, get the ID
    // if event location does not exist, add it
    
    $event_location_id = get_existing_event_loc($event_location);
    
    if ($event_location_id === null) {
        $event_location_id = add_event_loc($event_location); // add event location into database
    }


    // handle event----------------------------------------------------------------------------------------

    // handle sequence number---------------------------------
    $event_seq = 1; // default sequence number

    // handle event time--------------------------------------

    $event_start = trim($_POST['event_date_start']);    // is a datetime in the format 2001-12-16T16:00
    $event_end = trim($_POST['event_date_end']);        // same as above

    $event_start = convert_datetime_to_db($event_start);
    $event_end = convert_datetime_to_db($event_end);

    if ($event_start === null || $event_end === null) {
        $PDO->rollBack();
        redirectToPreviousPage("400");
    }

    // handle uid----------------------------------------------
    $event_uid = generate_event_id();

    // handle other dates--------------------------------------
    try {
        $event_created = new DateTime('now', new DateTimeZone('UTC'));
    } catch (Exception $e) {
        $PDO->rollBack();
        redirectToPreviousPage("400");
    }
    $event_created = $event_created->format('Y-m-d H:i:s');
    $event_modified = $event_created; // same as created, since it's a new event

    // handle description--------------------------------------

    $event_desc = prepare_event_desc_override($_POST);

    $event_desc_de = $event_desc[0];
    $event_desc_en = $event_desc[1];

    // insert into database----------------------------------------------------------------------------

    $sql = "INSERT INTO events (event_type_id, event_location_id, date_start, date_end, uid, date_created, date_modified, desc_de_override, desc_en_override, sequence) 
            VALUES (:event_type_id, :event_location_id, :date_start, :date_end, :uid, :date_created, :date_modified, :desc_de_override, :desc_en_override, :sequence)";
    $stmt = $PDO->prepare($sql);
    $stmt->execute([
        ':event_type_id' => $event_type_id,
        ':event_location_id' => $event_location_id,
        ':date_start' => $event_start,
        ':date_end' => $event_end,
        ':uid' => $event_uid,
        ':date_created' => $event_created,
        ':date_modified' => $event_modified,
        ':desc_de_override' => $event_desc_de,
        ':desc_en_override' => $event_desc_en,
        ':sequence' => $event_seq
    ]);

    $event_id = $PDO->lastInsertId();

    $PDO->commit();
    //$PDO->rollBack();

    // TODO: Singleton pattern for ICSGenerator
    $ICSGen = new ICSGenerator();
    $ICSGen->generateICS();

    $url_params = [
        'id' => $event_id
    ];
    redirectStatus("/calendar.php", "200/Added event: " . $event_name_en, $url_params);
}
redirectStatus("/", "401");
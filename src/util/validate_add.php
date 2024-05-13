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
/* @var bool $loggedIn */

/*
if (!$loggedIn) {
    redirectError("/", 334);
}
*/

// ----------------- LOGIN CHECK END -------------------

// ------------------- DATABASE CONNECTION -------------------

$dbConnection = new DBConnection();
$PDO = $dbConnection->useDB();

if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
    redirectError("/", "600");
}

// ----------------- DATABASE CONNECTION END -------------------


if (ENV === "dev") {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validate_event_details($_POST);

    $PDO->beginTransaction();

    // handle event name-----------------------------------------------------------

    $event_name_de = trim($_POST['event_name_de']);
    $event_name_en = trim($_POST['event_name_en']);

    // if event name already exists, get the ID
    // if event name does not exist, add it (but both names must be unique)

    $event_id = validate_existing_event_type($event_name_de, $event_name_en);

    if ($event_id === null) {
        $event_id = add_event_type($event_name_de, $event_name_en); // add event type into database
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

    $event_start = new DateTime($event_start, new DateTimeZone('Europe/Berlin'));
    $event_end = new DateTime($event_end, new DateTimeZone('Europe/Berlin'));
    $event_start->setTimezone(new DateTimeZone('UTC'));
    $event_end->setTimezone(new DateTimeZone('UTC'));

    $event_start = $event_start->format('Y-m-d H:i:s');
    $event_end = $event_end->format('Y-m-d H:i:s');

    // handle uid----------------------------------------------
    $event_uid = generate_event_id();

    // handle other dates--------------------------------------
    $event_created = new DateTime('now', new DateTimeZone('UTC'));
    $event_created = $event_created->format('Y-m-d H:i:s');
    $event_modified = $event_created; // same as created, since it's a new event

    // handle description--------------------------------------

    $enable_desc = false;
    if (isset($_POST['enable_desc']) && $_POST['enable_desc'] == "on") {
        $enable_desc = true;
    }

    $event_desc_de = "-"; // - = use no description at all
    if (isset($_POST['event_desc_de']) && $enable_desc) {
        $event_desc_de = trim($_POST['event_desc_de']); // if enable_desc is true & desc is set, use desc de override
    } else if (!isset($_POST['event_desc_de']) && $enable_desc) {
        $event_desc_de = NULL; // if enable_desc is true but desc is not set, use default desc
    }

    $event_desc_en = "-"; // - = use no description at all
    if (isset($_POST['event_desc_en']) && $enable_desc) {
        $event_desc_en = trim($_POST['event_desc_en']); // if enable_desc is true & desc is set, use desc en override
    } else if (!isset($_POST['event_desc_en']) && $enable_desc) {
        $event_desc_en = NULL; // if enable_desc is true but desc is not set, use default desc
    }


    // insert into database----------------------------------------------------------------------------

    $sql = "INSERT INTO events (event_type_id, event_location_id, date_start, date_end, uid, date_created, date_modified, desc_de_override, desc_en_override, sequence) 
            VALUES (:event_type_id, :event_location_id, :date_start, :date_end, :uid, :date_created, :date_modified, :desc_de_override, :desc_en_override, :sequence)";
    $stmt = $PDO->prepare($sql);
    $stmt->execute([
        ':event_type_id' => $event_id,
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

    if (ENV === "dev") {
        // print entire PDO transaction
        echo "Adding new event:<br>";
        echo "Event Title DE: $event_name_de / Event Title EN: $event_name_en / Event ID: $event_id<br>";
        echo "Event Location: $event_location / Event Location ID: $event_location_id<br>";
        echo "Event start: $event_start<br>";
        echo "Event end: $event_end<br>";
        echo "Event UID: $event_uid<br>";
        echo "Event created: $event_created<br>";
        echo "Event modified: $event_modified<br>";
        echo "Event desc DE: $event_desc_de<br>";
        echo "Event desc EN: $event_desc_en<br>";
        echo "Event sequence: $event_seq<br>";
    }

    $PDO->commit();
    //$PDO->rollBack();

    $ICSGen = new ICSGenerator();
    $ICSGen->generateICS();
}
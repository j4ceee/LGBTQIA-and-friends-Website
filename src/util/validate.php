<?php

use JetBrains\PhpStorm\NoReturn;

/**
 ** VALIDATION FUNCTIONS - AUTHENTICATION
 **/

function validate_login($username, $email, $password): void
{
    // check if required fields are set
    $requiredFields = [
        'username' => $username,
        'email' => $email,
        'password' => $password
    ];

    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            redirectToPreviousPage("301/$field");
        }
    }

    // validate input format
    validate_username($username);
    validate_password($password);
    validate_email($email);
}

function validate_username($username): void
{
    if (strlen($username) > 20) {
        redirectToPreviousPage("302/username");
    }
}

function validate_password($password): void
{
    if (strlen($password) < 8) {
        redirectToPreviousPage("303/password");
    }
}

function validate_email($email): void
{
    if (strlen($email) > 50) {
        redirectToPreviousPage("302/email");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectToPreviousPage("300/email");
    }
}

/**
 ** VALIDATION FUNCTIONS - EVENT DETAILS
 **/

function validate_event_details($post): void
{
    // required fields:
    /*
     * Event Start (DateTime)
     * Event End (DateTime)
     * Event Location (String)
     * Event Title DE (String)
     * Event Title EN (String)
     */

    // optional fields:
    /*
     * Button: Use Description? (Bool)
     * Event Description DE (String)
     * Event Description EN (String)
     */
    // if button is checked, use description if not empty, else use default description from database

    // check if required fields are set
    $requiredFields = [
        'event_start' => $post['event_date_start'],
        'event_end' => $post['event_date_end'],
        'event_location' => $post['event_location'],
        'event_name_de' => $post['event_name_de'],
        'event_name_en' => $post['event_name_en']
    ];

    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            redirectToPreviousPage("301/$field");
        }
    }

    // validate input format
    validate_event_date_time([$post['event_date_start'], $post['event_date_end']]);

    // check string length
    check_string_length($post);

    // check if event type exists in database
}

function validate_event_date_time(array $dateTimes): void
{
    // check if string is a valid date, like 2001-12-16T16:00
    foreach ($dateTimes as $dateTime) {
        if (!preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $dateTime)) {
            redirectToPreviousPage("500/eventDate");
        }
    }

    // check if end date is after start date
    if (strtotime($dateTimes[0]) >= strtotime($dateTimes[1])) { // if end date is before or equal to start date
        redirectToPreviousPage("502");
    }
}

function check_string_length($post): void
{
    $stringLengths = [
        'event_location' => 100,
        'event_name_de' => 50,
        'event_name_en' => 50,
        'event_desc_de' => 1500,
        'event_desc_en' => 1500
    ];

    foreach ($stringLengths as $field => $length) {
        if (isset($post[$field]) && strlen($post[$field]) > $length) {
            redirectToPreviousPage("501/$field");
        }
    }
}

/**
 ** VALIDATION FUNCTIONS - EXISTING ENTRIES
 **/

function validate_existing_event_type($event_type_de, $event_type_en): int|null
{
    $loc_id_de = get_existing_event_type($event_type_de, "de");
    $loc_id_en = get_existing_event_type($event_type_en, "en");

    // both loc_ids need to be the same (either null or the same int)
    if ($loc_id_de !== $loc_id_en) {
        redirectToPreviousPage("505");
    } else {
        return $loc_id_de;
        // return the event type id if it exists, else null
    }
}


function get_existing_event_type($event_type, $lang): int|null
{
    global $PDO;

    // check if event type exists in database
    $sql = "SELECT 
                id 
            FROM 
                event_types 
            WHERE 
                name_$lang = :event_type";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':event_type', $event_type);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 0) {
        return null;
    } else {
        return $result[0]['id'];
    }
}

function get_existing_event_loc(string $event_location): int|null
{
    global $PDO;

    // check if event location exists in database
    $sql = "SELECT 
                id 
            FROM 
                event_locations 
            WHERE 
                name = :event_location";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':event_location', $event_location);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) === 0) {
        return null;
    } else {
        return $result[0]['id'];
    }
}

/**
 ** VALIDATION FUNCTIONS - PREPARE DATA FOR DATABASE
 **/

function convert_datetime_to_db(string $datetime): ?string
{
    // datetime from input
    // datetime is in the format 2001-12-16T16:00
    try {
        $date = new DateTime($datetime, new DateTimeZone('Europe/Berlin'));
    } catch (Exception) {
        return null;
    }

    $date->setTimezone(new DateTimeZone('UTC'));
    return $date->format('Y-m-d H:i:s');
    // output datetime is in the format 2001-12-16 16:00:00
}

function convert_datetime_to_ui(string $datetime): ?string
{
    // datetime from database
    // datetime is in the format 2001-12-16 16:00:00
    try {
        $date = new DateTime($datetime, new DateTimeZone('UTC'));
    } catch (Exception) {
        return null;
    }

    $date->setTimezone(new DateTimeZone('Europe/Berlin'));
    return $date->format('Y-m-d\TH:i');
    // output datetime is in the format 2001-12-16T16:00
}

function prepare_event_desc_override($post): array
{
    $desc_de = "-"; // - = use no description at all
    $desc_en = "-"; // - = use no description at all

    $use_desc = false;
    if (isset($post['enable_desc']) && $post['enable_desc'] === "on") {
        $use_desc = true;
    }

    if ($use_desc && isset($post['event_desc_de']) && $post['event_desc_de'] !== "" && $post['event_desc_de'] !== "-") {
        $desc_de = trim($post['event_desc_de']); // if enable_desc is true & desc is set, use desc de override
    } else if ($use_desc && (!isset($post['event_desc_de']) || $post['event_desc_de'] === "" || $post['event_desc_de'] === "-")) {
        $desc_de = ""; // if enable_desc is true but desc is not set, use default desc (set override to null)
    }

    if ($use_desc && isset($post['event_desc_en']) && $post['event_desc_en'] !== "" && $post['event_desc_en'] !== "-") {
        $desc_en = trim($post['event_desc_en']); // if enable_desc is true & desc is set, use desc en override
    } else if ($use_desc && (!isset($post['event_desc_en']) || $post['event_desc_en'] === "" || $post['event_desc_en'] === "-")) {
        $desc_en = ""; // if enable_desc is true but desc is not set, use default desc (set override to null)
    }

    return [$desc_de, $desc_en]; // get a specific desc with [0] or [1]
}
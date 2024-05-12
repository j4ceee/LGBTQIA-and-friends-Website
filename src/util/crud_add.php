<?php
function add_event_type($event_name_de, $event_name_en): int
{
    global $PDO;

    $sql = "INSERT INTO event_types (name_de, name_en, desc_de, desc_en) VALUES (:event_name_de, :event_name_en, NULL, NULL)";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':event_name_de', $event_name_de);
    $stmt->bindParam(':event_name_en', $event_name_en);
    $stmt->execute();

    return $PDO->lastInsertId();
}

function add_event_loc(string $event_location): int
{
    global $PDO;

    $sql = "INSERT INTO event_locations (name) VALUES (:event_location)";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':event_location', $event_location);
    $stmt->execute();

    return $PDO->lastInsertId();
}

function generate_event_id(): string
{
    global $PDO;
    $is_unique = false;
    $event_uid = "";

    do {
        // generate UID with 64 characters including ending (@lgbt-hs-ansbach.de)
        try {
            $event_uid = bin2hex(random_bytes(22)) . "@lgbt-hs-ansbach.de";
        } catch (\Random\RandomException $e) {
            continue; // try again if random_bytes() fails
        }

        if ($event_uid !== "") {
            // check if the UID is unique
            $sql = "SELECT 
                        id 
                    FROM 
                        events 
                    WHERE 
                        uid = :event_uid";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':event_uid', $event_uid);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) === 0) {
                $is_unique = true;
            }
        }
    } while (!$is_unique);

    return $event_uid;
}
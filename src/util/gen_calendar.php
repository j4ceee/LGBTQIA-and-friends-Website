<?php
// generate frontend calendar

function gen_calendar($lang, int $headerLevel = 2, bool $admin = false)
{
    echo "<section class='calendar'>";
        echo "<div class='section_header'>";
        echo "<h".$headerLevel." class='section_heading'>" . lang_strings['events'] . "</h".$headerLevel.">";
        echo "<div class='section_header_underline'></div>";
        echo "</div>";

        echo "<div class='calendar_container'>";
            echo "<ul class='calendar_list'>";

            // get all events
            $events = get_all_events();

            if ($events === false) {
                echo "<li class='calendar_item'>";
                echo "<p class='calendar_item_name'>" . lang_strings['no_events'] . "</p>";
                echo "</li>";
            } else {
                foreach ($events as $event) {
                    /**
                     * Event Name Formatting
                     */
                    // get event name depending on language
                    $event_name = htmlspecialchars($event['name_'.$lang]);

                    /**
                     * Location Formatting
                     */
                    // get event location
                    $event_location = htmlspecialchars($event['location_name']);

                    /**
                     * Description Formatting
                     */
                    // get event description depending on language, use default if no override, use no description if override is "-", use no description if no default
                    $event_desc = "";
                    if ($event['desc_'.$lang.'_override'] === "-") {
                        // do nothing, no description
                    } else if ($event['desc_'.$lang.'_override'] === NULL || $event['desc_'.$lang.'_override'] === "") {
                        $event_desc = htmlspecialchars($event['desc_'.$lang.'_default']);
                    } else {
                        $event_desc = htmlspecialchars($event['desc_'.$lang.'_override']);
                    }

                    /**
                     * Time & Date Formatting
                     */
                    // format date start as day, month int -1, year
                    try {
                        $date_start = new DateTime(htmlspecialchars($event['date_start']));
                    } catch (Exception $e) {
                        if ($admin) {
                            echo "<li class='calendar_item'>";
                            echo "<p class='calendar_item_name'> Error with Event: " . $event_name . "</p>";
                            echo "<p class='calendar_item_desc'>" . $e . "</p>";
                            echo "</li>";
                        }
                        continue; // skip event if date is invalid
                    }

                    $date_start_day = $date_start->format('d');

                    $date_start_month = $date_start->format('m') - 1;
                    // get month name from lang_strings
                    $date_start_month = lang_strings['months'][$date_start_month];

                    $date_start_year = $date_start->format('Y');

                    // get event start time depending on language
                    if ($lang === 'de') {
                        $date_start_time = $date_start->format('H:i');
                    } else {
                        $date_start_time = $date_start->format('h:i a');
                    }

                    /**
                     * Output
                     */
                    echo "<li class='calendar_item'>";
                        echo "<div class='calendar_item_date'>";
                            echo "<p class='calendar_item_day'>" . $date_start_day . "</p>";
                            echo "<p class='calendar_item_month'>" . $date_start_month . "</p>";
                            echo "<p class='calendar_item_year'>" . $date_start_year . "</p>";
                        echo "</div>";

                        echo "<div class='calendar_item_info'>";
                            echo "<p class='calendar_item_name'>" . $event_name . "</p>";
                            // desc container should always be there, even if empty, for styling purposes
                            echo "<p class='calendar_item_desc'>" . nl2br($event_desc) . "</p>";
                        echo "</div>";

                        echo "<div class='calendar_item_location'>";
                            echo "<p class='calendar_item_loc_time'>" . $date_start_time . "</p>";
                            echo "<p class='calendar_item_loc_name'>" . $event_location . "</p>";
                        echo "</div>";
                    echo "</li>";
                }
            }



    echo "</section>";
}

function get_all_events(): false|array
{
    global $PDO;
    $sql = "SELECT
                e.date_start,
                e.date_end,
                e.desc_de_override,
                e.desc_en_override,
                et.desc_de AS desc_de_default,
                et.desc_en AS desc_en_default,
                et.name_de AS name_de,
                et.name_en AS name_en,
                el.name AS location_name
            FROM
                events e
            JOIN
                event_types et
            ON
                e.event_type_id = et.id
            JOIN
                event_locations el
            ON
                e.event_location_id = el.id
            WHERE
                date_end >= NOW()
            ORDER BY
                date_start";

    $stmt = $PDO->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
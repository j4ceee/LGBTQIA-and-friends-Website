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
            // get all events
            $events = get_all_events();

            if ($events === false || count($events) === 0) {
                echo "<ul class='calendar_list calendar_error'>";
                echo "<p class='calendar_item_name'>" . lang_strings['no_events'] . "</p>";
            } else {
                if (count($events) > 2) {
                    echo "<ul class='calendar_list calendar_large'>";
                } else {
                    echo "<ul class='calendar_list calendar_small'>";
                }

                foreach ($events as $event) {
                    /**
                     * Event Name Formatting
                     */
                    // get event name depending on language
                    $event_name = htmlspecialchars($event['name_'.$lang]);

                    // get event id
                    $event_id = htmlspecialchars($event['id']);

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
                        // convert to local timezone (Berlin)
                        $date_start->setTimezone(new DateTimeZone('Europe/Berlin'));
                    } catch (Exception $e) {
                        if ($admin) {
                            echo "<li class='calendar_item'>";
                            echo "<p class='calendar_item_name'> Error with Event: " . $event_name . "</p>";
                            echo "<p class='calendar_item_desc'>" . $e . "</p>";
                            echo "</li>";
                        }
                        continue; // skip event if date is invalid
                    }

                    // get day depending on language (e.g. 1. for de, 1st for en)
                    if ($lang === 'de') {
                        $date_start_day = $date_start->format('j.');
                    } else {
                        $date_start_day = $date_start->format('jS');
                    }

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
                    echo "<li class='calendar_item_cont'>";
                    if ($event_desc !== "") {
                        echo "<div class='calendar_item calendar_button' id='calendar_button_".$event_id."' data-event-id='".$event_id."' aria-expanded='false'>";
                    } else {
                        echo "<div class='calendar_item'>";
                    }
                            echo "<time class='calendar_item_date' datetime='".$date_start->format('Y-m-d')."'>";
                                if ($lang === 'de') {
                                    echo "<p class='calendar_item_day'>" . $date_start_day . "</p>";
                                    echo "<p class='calendar_item_month'>" . $date_start_month . "</p>";
                                } else {
                                    echo "<p class='calendar_item_month'>" . $date_start_month . "</p>";
                                    echo "<p class='calendar_item_day'>" . $date_start_day . "</p>";
                                }
                                echo "<p class='calendar_item_year'>" . $date_start_year . "</p>";
                            echo "</time>";

                            echo "<div class='calendar_item_info'>";
                                echo "<p class='calendar_item_name'>" . $event_name . "</p>";
                                // desc container should always be there, for styling purposes, if empty -> empty p
                                if ($event_desc === "") {
                                    echo "<p class='calendar_item_desc'></p>";
                                } else {
                                    echo "<details id='event_det_".$event_id."' class='calendar_item_desc'><summary class='calendar_item_desc_ctrl' data-event-id='".$event_id."'>Details</summary>" . nl2br($event_desc) . "</details>";
                                }

                            echo "</div>";

                            echo "<div class='calendar_item_location'>";
                                echo "<time class='calendar_item_loc_time' datetime='".$date_start->format('H:i')."'>" . $date_start_time . "</time>";
                                echo "<p class='calendar_item_loc_name' lang='de'>" . $event_location . "</p>";
                            echo "</div>";
                        echo "</div>";
                    echo "</li>";
                }
            }
    echo "</ul>";

    // get all ics files with the following names: lgbt-hs-ansbach-events-LANG.ics / lgbt-hs-ansbach-events-YEAR-LANG.ics
    $ics_filepath = dirname(__DIR__) . '/cal/';
    $ics_main_file = glob($ics_filepath . 'lgbt-hs-ansbach-events-'.$lang.'.ics');
    // get all year files
    $ics_year_files = glob($ics_filepath . 'lgbt-hs-ansbach-events-[0-9][0-9][0-9][0-9]-'.$lang.'.ics');
    rsort($ics_year_files);


    if (count($ics_main_file) > 0 && count($ics_year_files) > 0) {
        // options to copy calendar link to clipboard
        echo "<details>";
            echo "<summary class='ical_options_toggle'>". lang_strings['show_ical_controls'] ."</summary>";
            echo "<div class='calendar_link_copy_cont'>";
                echo "<div class='default_calendar_copy'>";
                    echo "<p class='calendar_item_name ical_options_header'>" . lang_strings['default_cal_header'] . "</p>";

                    echo "<div class='calendar_copy_buttons'>";
                        echo "<a id='default_calendar_link' class='ical_copy_button' href='".BASE_URL."/cal/lgbt-hs-ansbach-events-".$lang.".ics' download='lgbt-hs-ansbach-events-".$lang.".ics'>".lang_strings['download']."</a>";
                        echo "<button class='ical_copy_button' id='default_calendar_copy_button'>";
                            //echo "<img class='default_calendar_copy_icon' src='./img/copy_icon.svg' alt='Copy Icon'>";
                            echo lang_strings['copy_link'];
                        echo "</button>";
                    echo "</div>";
                echo "</div>";
                echo "<div class='year_calendar_copy'>";
                    echo "<p class='calendar_item_name ical_options_header'>" . lang_strings['year_cal_header'] . "</p>";

                    echo "<label for='year_calendar_select' class='year_calendar_select_label'>". lang_strings['year_cal_select_label'] ."</label>";
                    // generate dropdown for year files
                    $latest_year = substr($ics_year_files[0], -11, 4);
                    echo "<select id='year_calendar_select' class='year_calendar_select'>";
                        foreach ($ics_year_files as $file) {
                            $year = substr($file, -11, 4);
                            echo "<option data-file='lgbt-hs-ansbach-events-".$year."-".$lang.".ics' value='".BASE_URL."/cal/lgbt-hs-ansbach-events-".$year."-".$lang.".ics'>". $year ."</option>";
                        }
                    echo "</select>";

                    echo "<div class='calendar_copy_buttons'>";
                        echo "<a id='year_calendar_link' class='ical_copy_button' href='".BASE_URL."/cal/lgbt-hs-ansbach-events-".$latest_year."-".$lang.".ics' download='lgbt-hs-ansbach-events-".$latest_year."-".$lang.".ics'>".lang_strings['download']."</a>";
                        echo "<button class='ical_copy_button' id='year_calendar_copy_button'>";
                            //echo "<img class='year_calendar_copy_icon' src='./img/copy_icon.svg' alt='Copy Icon'>";
                            echo lang_strings['copy_link'];
                        echo "</button>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</details>";
    }
    echo "</section>";
}

function get_all_events(): false|array
{
    global $PDO;
    global $dbConnection;

    if ($PDO === null || $dbConnection->checkDBSchema() !== true) {
        return false;
    }

    $sql = "SELECT
                e.id,
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
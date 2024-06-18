<?php

class ICSGenerator
{
    private array $languages = ["de", "en"];
    private array $years = [];

    public function generateICS(): void
    {
        // get all years between 2024 and the current year
        $this->years = range(2024, date("Y"));

        $filepath = dirname(__DIR__) . '/cal/';

        // generate ICS file including events from the past 12 months & next 8 months
        foreach ($this->languages as $lang) {
            $ics = $this->genICSheader($lang);
            $events = $this->getEvents($lang);
            $ics .= $this->genICSbody($events);
            $ics .= $this->genICSfooter();

            // save ICS file

            $filename = "lgbt-hs-ansbach-events-$lang.ics";
            file_put_contents($filepath . $filename, $ics);
        }

        // generate ICS files for each language and entire year
        foreach ($this->years as $year) {
            foreach ($this->languages as $lang) {
                $ics = $this->genICSheader($lang, $year);
                $events = $this->getEvents($lang, $year);
                $ics .= $this->genICSbody($events);
                $ics .= $this->genICSfooter();

                // save ICS file
                $filename = "lgbt-hs-ansbach-events-$year-$lang.ics";
                file_put_contents($filepath . $filename, $ics);
            }
        }

    }

    private function genICSheader(string $lang, string $year = ""): string
    {
        if ($lang === "de") {
            $calname = "LGBTQIA+ & friends Terminplan";
            if ($year !== "") {
                $calname .= " $year";
            }
            $caldesc = 'Terminplan';
            if ($year !== "") {
                $caldesc .= " $year";
            }
            $caldesc .= ' der queeren Jugendgruppe "LGBTQIA+ & friends" der HS Ansbach';
        }
        else if ($lang === "en") {
            $calname = "LGBTQIA+ & friends Events";
            if ($year !== "") {
                $calname .= " $year";
            }

            $caldesc = 'Events';
            if ($year !== "") {
                $caldesc .= " $year";
            }
            $caldesc .= ' of the queer youth group "LGBTQIA+ & friends" of the Ansbach University';
        }

        //uppercase the language
        $lang = strtoupper($lang);

        $header = "BEGIN:VCALENDAR\r\n";
        $header .= "PRODID:-//LGBT-HS-Ansbach//LGBT-HS-Ansbach//$lang\r\n";
        $header .= "VERSION:2.0\r\n";
        $header .= "CALSCALE:GREGORIAN\r\n";
        $header .= "METHOD:PUBLISH\r\n";
        $header .= $this->formatLineLength("X-WR-CALNAME:$calname");
        $header .= "X-WR-TIMEZONE:Europe/Berlin\r\n";
        $header .= $this->formatLineLength("X-WR-CALDESC:$caldesc");

        return $header;
    }

    private function genICSfooter(): string
    {
        $footer = "END:VCALENDAR";
        return $footer;
    }

    private function getEvents(string $lang, string $year = ""): array
    {
        global $PDO;

        $events = [];

        $sql = "SELECT 
                    e.date_start,
                    e.date_end,
                    e.uid,
                    e.date_created,
                    e.date_modified,
                    e.desc_{$lang}_override as event_desc,
                    e.sequence,
                    et.name_$lang AS event_type,
                    et.desc_$lang AS event_type_desc,
                    el.name AS event_location
                FROM
                    events e
                JOIN
                    event_types et
                ON
                    e.event_type_id = et.id
                JOIN
                    event_locations el
                ON
                    e.event_location_id = el.id";

        if ($year !== "") {
            $sql .= " WHERE YEAR(e.date_start) = :year";
        } else {
            // get events from the past 12 months and the next 8 months
            $sql .= " WHERE e.date_start >= DATE_SUB(NOW(), INTERVAL 12 MONTH) AND e.date_start <= DATE_ADD(NOW(), INTERVAL 8 MONTH)";
        }

        $stmt = $PDO->prepare($sql);
        if ($year !== "") {
            $stmt->bindParam(':year', $year);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $events[] = $row;
        }

        return $events;
    }

    private function genICSbody(array $events): string
    {
        $ics_body = "";
        foreach ($events as $event) {
            $ics_event = "BEGIN:VEVENT\r\n";
            $ics_event .= "DTSTART:" . $this->formatDateTime($event['date_start']) . "\r\n";
            $ics_event .= "DTEND:" . $this->formatDateTime($event['date_end']) . "\r\n";
            //DTSTAMP is the current time when this file is generated
            $ics_event .= "DTSTAMP:" . $this->formatDateTime(date("Y-m-d H:i:s")) . "\r\n";
            $ics_event .= "UID:" . $event['uid'] . "\r\n";
            $ics_event .= "CREATED:" . $this->formatDateTime($event['date_created']) . "\r\n";
            $ics_event .= "LAST-MODIFIED:" . $this->formatDateTime($event['date_modified']) . "\r\n";

            // for description:
            // if description has CRLF linebreaks, replace them with literal "\n"
            $event_desc_ovr = $event['event_desc'];
            if ($event['event_desc'] !== NULL && $event['event_desc'] !== "" && $event['event_desc'] !== "-") {
                $event_desc_ovr = str_replace(["\r\n", "\n"], "\\n", $event['event_desc']);
            }

            $event_desc_def = $event['event_type_desc'];
            if ($event['event_type_desc'] !== NULL && $event['event_type_desc'] !== "") {
                $event_desc_def = str_replace(["\r\n", "\n"], "\\n", $event['event_type_desc']);
            }

            /* if $event['event_desc'] is "-" -> use no description at all
             * if desc is NULL -> use default description ($event['event_type_desc'] here)
             * if $event['event_desc'] is set -> use this description
             */
            if ($event['event_desc'] === "-") {
                // do nothing, no description
            } else if ($event['event_desc'] === NULL || $event['event_desc'] === "") {
                $ics_event .= $this->formatLineLength("DESCRIPTION:" . $event_desc_def);
            } else {
                $ics_event .= $this->formatLineLength("DESCRIPTION:" . $event_desc_ovr);
            }

            $ics_event .= $this->formatLineLength("LOCATION:" . $event['event_location']);
            $ics_event .= "SEQUENCE:" . $event['sequence'] . "\r\n";
            $ics_event .= "STATUS:CONFIRMED\r\n";
            $ics_event .= $this->formatLineLength("SUMMARY:" . $event['event_type']);
            $ics_event .= "TRANSP:OPAQUE\r\n";
            $ics_event .= "END:VEVENT\r\n";

            $ics_body .= $ics_event;
        }

        return $ics_body;
    }

    private function formatLineLength(string $textblock): string
    {
        // split the textblock into lines with a maximum length of 75 characters, excluding linebreak CRLF
        // the newly created line should start with a space
        $textblock = wordwrap($textblock, 74, "\r\n ", true);

        //add linrbreak at the end
        $textblock .= "\r\n";

        return $textblock;
    }

    private function formatDateTime(string $datetime): string
    {
        // datetime format from the database: "YYYY-MM-DD HH:MM:SS" / "Y-m-d H:i:s" in PHP
        // ICS format: "YYYYMMDDTHHMMSSZ", "T" = date/time separator, "Z" = UTC time
        $datetime = str_replace(" ", "T", $datetime);
        $datetime = str_replace("-", "", $datetime);
        $datetime = str_replace(":", "", $datetime);
        $datetime .= "Z";

        return $datetime;
    }

}
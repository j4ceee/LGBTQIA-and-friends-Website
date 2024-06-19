<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * REDIRECT FUNCTIONS
 */

#[NoReturn] function redirect($path = '/', array $queries = null): void
{
    // rebuild url to properly set queries
    $url = parse_url(BASE_URL . $path);

    if ($queries !== null) {
        $url['query'] = http_build_query($queries);
    }

    if ($url['query'] !== null) {
        $url = $url['path'] . '?' . $url['query'];
    } else {
        $url = $url['path'];
    }

    //redirect to the given path, '' = home
    if ($url === '/' || $url === '') {
        header("Location: " . BASE_URL);
    }
    else {
        header("Location: " . BASE_URL . $url);
    }
    exit();
}

#[NoReturn] function redirectStatus($path = '/', string $msg = '', array $query = null): void
{
    if ($msg !== '') {
        $query['status'] = $msg;
        redirect($path, $query);
    }

    redirect($path);
}

#[NoReturn] function redirectToPreviousPage(string $msg): void
{
    $path = '/'; // default path
    $urlParams = ''; // default URL parameters

    if(isset($_SERVER['HTTP_REFERER'])) {
        // get the previous page URL
        $url_tmp = $_SERVER['HTTP_REFERER'];
        $url = parse_url($url_tmp);

        // check if the previous page is on the same domain
        // if not on the same domain, use home page
        if ($url['host'] === SERVERNAME) {
            $path = $url['path']; // get the path from the URL
            $urlParams = $url['query']; // get the query string from the URL

        }
    }

    parse_str($urlParams, $urlParams); // convert the query string to an associative array

    if ($msg !== '') {
        $urlParams['status'] = $msg; // add the error message to the URL parameters / overwrite it if it already exists
    }

    if (count($urlParams) !== 0) {
        redirect($path, $urlParams); // redirect to the previous page with the error message
    } else {
        redirect($path); // redirect to the previous page
    }
}

/**
 * ERROR MESSAGE FUNCTIONS
 */

$errorDict = [
    // missing inputs errors
    "400" => "Error! Inputs were not submitted correctly.",
    "401" => "Error! No data was submitted.",
    "404" => "Error! Missing required field: ",

    // login errors
    "300" => "Error! Invalid ",
    "301" => "Error! Missing ",
    "302" => "Error! Too long: ",
    "303" => "Error! Too short: ",
    "333" => "Error! Login failed. Please check your credentials and try again.",
    "334" => "Error! You need to be logged in to access this page.",

    // input format errors
    "500" => "Error! Invalid input format for field: ",
    "501" => "Error! Input too long for field: ",

    "502" => "Error! End date must be after start date.",
    "503" => "Error! One of the event names already exists in the database.",
    "504" => "Error! No event details were changed.",
    "505" => "Error! Event names do not match.",

    // database errors
    "600" => "Error! Database not set up correctly.",
    "601" => "Error! No event found with ID: ",

    // success messages
    "200" => "Success! ",
];

function getErrorMsg(bool $alertError = true): string
{
    $message = "";

    if (isset($_GET['status'])) {
        $status = (string)$_GET['status'];

        // queries are in the format: status=200%2FEdited+event+details
        // clean up the status query
        $status = urldecode($status);

        // get the error code
        $code = (int)substr($status, 0, 3);

        if (strlen($code) === 3 && $code >= 200 && $code <= 999) {
            // get the error message
            $info = substr($status, 4);

            // sanitize status (remove all characters except numbers, letters, :, _, -, space, and éÉ
            $info = preg_replace('/[^a-zA-Z0-9éÉ: &_-]/', '', $info); // TODO: better sanitization

            // & gets filtered out by htmlspecialchars, so replace it with the word 'and'
            $info = str_replace('&', 'and', $info);

            // separate camelCase words with spaces
            $info = preg_replace('/(?<! )[A-Z]/', ' $0', $info);

            // make first letter after underscore uppercase
            $info = str_replace('_', ' ', $info);

            // capitalize first letter
            $info = ucfirst($info);

            // if the error code is in the error dictionary, return the corresponding error message
            if (array_key_exists($code, $GLOBALS['errorDict'])) {
                $message = $GLOBALS['errorDict'][$code] . $info;
            } else {
                $message = "Error! Unknown error occurred.";
            }

            if ($alertError) {
                echo "<script>alert('". htmlspecialchars($message) ."');</script>";
            }
        }
    }

    return $message;
}
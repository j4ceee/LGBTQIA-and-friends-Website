<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * REDIRECT FUNCTIONS
 */

#[NoReturn] function redirect($path = '/', string $msg = ''): void
{
    //redirect to the given path, '' = home
    header("Location: " . BASE_URL . $path);
    exit();
}

#[NoReturn] function redirectError($path = '/', string $msg = ''): void
{
    if ($msg !== '') {
        $path .= '?status=' . $msg;
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
        redirect($path . '?' . http_build_query($urlParams)); // redirect to the previous page with the error message
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

    // database errors
    "600" => "Error! Database not set up correctly.",
];

function getErrorMsg(bool $alertError = true): string
{
    $message = "";

    if (isset($_GET['status'])) {
        $status = (string)$_GET['status'];

        // sanitize status (remove all characters except numbers, letters, :, _, -)
        $status = preg_replace('/[^a-zA-Z0-9éÉ:_-]/', '', $status); // TODO: better sanitization

        // first 3 characters of status are the error code
        $code = substr($status, 0, 3);

        // everything after / is the error message
        $info = substr($status, 3);

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

    return $message;
}
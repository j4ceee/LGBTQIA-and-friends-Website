<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * REDIRECT FUNCTIONS
 */


#[NoReturn] function redirect($path = ''): void
{
    //redirect to the given path, '' = home
    echo BASE_URL . $path;
    header("Location: " . BASE_URL . $path);
    exit();
}

#[NoReturn] function redirectToPreviousPage($msg): void
{
    if(isset($_SERVER['HTTP_REFERER'])) {
        // get the previous page URL
        $url = $_SERVER['HTTP_REFERER'];
    } else { // if there is no previous page
        $url = BASE_URL; // redirect to the home page
    }

    $strippedURL = strtok($url, '?'); // remove query string from URL

    $urlParams = parse_url($url, PHP_URL_QUERY); // get the query string from the URL
    parse_str($urlParams, $urlParams); // convert the query string to an associative array

    $urlParams['status'] = $msg; // add the error message to the URL parameters / overwrite it if it already exists

    $url = $strippedURL . '?' . http_build_query($urlParams); // rebuild the URL with the new parameters

    // redirect to the previous page
    header('Location: ' . $url);

    //echo "Redirecting to: $url";
    exit();
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
];

function getErrorMsg(bool $alertError = true): string
{
    $message = "";

    if (isset($_GET['status'])) {
        $status = (string)$_GET['status'];

        // sanitize status (remove all characters except numbers & unicode letters)
        $status = preg_replace('/[^a-zA-Z0-9éÉ :-]/', '', $status); // TODO: better sanitization
        // echo "<p>Status: $status</p>";

        // first 3 characters of status are the error code
        $code = substr($status, 0, 3);

        // everything after / is the error message
        $info = substr($status, 3);

        // separate camelCase words with spaces
        $info = preg_replace('/(?<! )[A-Z]/', ' $0', $info);

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
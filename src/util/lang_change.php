<?php
/** do not include this file
 *
 *  this page gets loaded by the user when they change the language
 */
require_once(__DIR__ . '/conf.php'); // start session
require_once(__DIR__ . '/utils.php');

// get lang from URL
if (!isset($_GET['lang'])) {
    redirect(); // redirect to home page
}

$lang = $_GET['lang'];

require_once(__DIR__ . '/auth_session_start.php'); // start session

$accepted_langs = ['en', 'de'];

if (!in_array($lang, $accepted_langs)) {
    redirect(); // redirect to home page
}

// set lang in session
$_SESSION['lang'] = $lang;

// redirect to previous page
require_once(__DIR__ . '/utils.php');
redirectToPreviousPage('');

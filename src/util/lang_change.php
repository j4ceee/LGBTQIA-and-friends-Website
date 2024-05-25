<?php

// get lang from URL
$lang = $_GET['lang'];

require_once(__DIR__ . '/auth_session_start.php'); // start session

// set lang in session
$_SESSION['lang'] = $lang;

// redirect to previous page
require_once(__DIR__ . '/utils.php');
redirectToPreviousPage('');

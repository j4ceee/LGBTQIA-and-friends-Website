<?php
if (!isset($_SESSION['lang'])) {
    $user_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $accept_lang = ['de', 'en'];
    $user_lang = in_array($user_lang, $accept_lang) ? $user_lang : 'en'; // if user language is not supported, default to English
    $_SESSION['lang'] = $user_lang;
}

$lang = $_SESSION['lang'];

require_once(__DIR__ . '/lang/' . $lang . '.php');
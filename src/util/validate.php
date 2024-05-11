<?php

use JetBrains\PhpStorm\NoReturn;

/*
 * VALIDATION FUNCTIONS - AUTHENTICATION
 */

function validate_login($username, $email, $password): void
{
    // check if required fields are set
    $requiredFields = [
        'username' => $username,
        'email' => $email,
        'password' => $password
    ];

    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            redirectToPreviousPage("301/$field");
        }
    }

    // validate input format
    validate_username($username);
    validate_password($password);
    validate_email($email);
}

function validate_username($username): void
{
    if (strlen($username) > 20) {
        redirectToPreviousPage("302/username");
    }
}

function validate_password($password): void
{
    if (strlen($password) < 8) {
        redirectToPreviousPage("303/password");
    }
}

function validate_email($email): void
{
    if (strlen($email) > 50) {
        redirectToPreviousPage("302/email");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirectToPreviousPage("300/email");
    }
}
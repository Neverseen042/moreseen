
<?php

require_once __DIR__ . "/../config/config.php";


/*
|--------------------------------------------------------------------------
| GET OR CREATE CSRF TOKEN
|--------------------------------------------------------------------------
*/

function csrfToken()
{
    if (
        !isset($_SESSION["csrf_token"]) ||
        empty($_SESSION["csrf_token"])
    ) {

        $_SESSION["csrf_token"] =
            bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}


/*
|--------------------------------------------------------------------------
| CSRF FORM FIELD
|--------------------------------------------------------------------------
*/

function csrfField()
{
    $token = csrfToken();

    return
        '<input
            type="hidden"
            name="csrf_token"
            value="' .
            htmlspecialchars(
                $token,
                ENT_QUOTES,
                "UTF-8"
            ) .
        '">';
}


/*
|--------------------------------------------------------------------------
| VERIFY CSRF TOKEN
|--------------------------------------------------------------------------
*/

function verifyCsrfToken($token)
{
    if (
        empty($token) ||
        empty($_SESSION["csrf_token"])
    ) {

        return false;
    }

    return hash_equals(
        $_SESSION["csrf_token"],
        $token
    );
}


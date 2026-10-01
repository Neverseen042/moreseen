<?php

require_once __DIR__ . "/../config/config.php";


/*
|--------------------------------------------------------------------------
| CHECK IF USER IS LOGGED IN
|--------------------------------------------------------------------------
*/

function isLoggedIn()
{
    return isset($_SESSION["user_id"]);
}


/*
|--------------------------------------------------------------------------
| REQUIRE LOGIN
|--------------------------------------------------------------------------
|
| Use this on pages that should only be accessible
| to logged-in users.
|
*/

function requireLogin()
{
    if (!isLoggedIn()) {

        header("Location: ../auth/login.php");
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| GET CURRENT USER ID
|--------------------------------------------------------------------------
*/

function currentUserId()
{
    return $_SESSION["user_id"] ?? null;
}


/*
|--------------------------------------------------------------------------
| GET CURRENT USER NAME
|--------------------------------------------------------------------------
*/

function currentUserName()
{
    return $_SESSION["user_name"] ?? "User";
}


/*
|--------------------------------------------------------------------------
| GET CURRENT USER EMAIL
|--------------------------------------------------------------------------
*/

function currentUserEmail()
{
    return $_SESSION["user_email"] ?? "";
}
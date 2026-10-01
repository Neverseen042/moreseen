
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

/*
|--------------------------------------------------------------------------
| Load .env
|--------------------------------------------------------------------------
*/

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

/*
|--------------------------------------------------------------------------
| App settings
|--------------------------------------------------------------------------
*/

define("APP_NAME", "Moreseen");
define("APP_URL", "http://localhost/moreseen");

define(
    "TMDB_IMAGE_URL",
    "https://image.tmdb.org/t/p/w500"
);

date_default_timezone_set("Africa/Lagos");

/*
|--------------------------------------------------------------------------
| Start session
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


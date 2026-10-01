<?php

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$tmdbApiKey = $_ENV["TMDB_API_KEY"];

$tmdbBaseUrl = "https://api.themoviedb.org/3";
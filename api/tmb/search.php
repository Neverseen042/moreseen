<?php

require_once __DIR__ . "/../../config/tmdb.php";

header("Content-Type: application/json");

$query = $_GET["query"] ?? "";

if (empty(trim($query))) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a search term."
    ]);
    exit;
}

$url = $tmdbBaseUrl . "/search/multi?api_key=" 
     . urlencode($tmdbApiKey)
     . "&query=" 
     . urlencode($query)
     . "&include_adult=false";

$response = file_get_contents($url);

if ($response === false) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to connect to TMDb."
    ]);
    exit;
}

$data = json_decode($response, true);

echo json_encode([
    "success" => true,
    "results" => $data["results"] ?? []
]);
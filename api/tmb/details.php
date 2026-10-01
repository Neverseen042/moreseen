<?php

require_once __DIR__ . "/../../config/tmdb.php";

header("Content-Type: application/json");

$id = $_GET["id"] ?? "";
$type = $_GET["type"] ?? "";

if (empty($id) || !in_array($type, ["movie", "tv"])) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid media ID or media type."
    ]);

    exit;
}

$url = $tmdbBaseUrl
    . "/" . $type
    . "/" . urlencode($id)
    . "?api_key="
    . urlencode($tmdbApiKey);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);

if ($response === false) {

    curl_close($ch);

    echo json_encode([
        "success" => false,
        "message" => "Unable to connect to TMDb."
    ]);

    exit;
}

curl_close($ch);

$data = json_decode($response, true);

if (isset($data["status_code"])) {

    echo json_encode([
        "success" => false,
        "message" => $data["status_message"] ?? "TMDb error."
    ]);

    exit;
}

echo json_encode([
    "success" => true,
    "result" => $data
]);

<?php
require_once __DIR__ . "/../helpers/csrf.php";
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to send a tip.");
}


/*
|--------------------------------------------------------------------------
| GET DATA
|--------------------------------------------------------------------------
*/

$senderId =
    (int) $_SESSION["user_id"];

$recommendationId =
    (int) ($_POST["recommendation_id"] ?? 0);

$amount =
    (int) ($_POST["amount"] ?? 0);


/*
|--------------------------------------------------------------------------
| VALIDATE BASIC DATA
|--------------------------------------------------------------------------
*/

if (
    $recommendationId <= 0 ||
    $amount <= 0
) {
    die("Invalid tip details.");
}


/*
|--------------------------------------------------------------------------
| GET RECOMMENDATION OWNER
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare("
    SELECT
        id,
        user_id
    FROM recommendations
    WHERE id = ?
    LIMIT 1
");

$query->execute([
    $recommendationId
]);

$recommendation =
    $query->fetch();


if (!$recommendation) {
    die("Recommendation not found.");
}


/*
|--------------------------------------------------------------------------
| GET REAL RECEIVER
|--------------------------------------------------------------------------
*/

$receiverId =
    (int) $recommendation["user_id"];


/*
|--------------------------------------------------------------------------
| PREVENT SELF-TIPPING
|--------------------------------------------------------------------------
*/

if ($senderId === $receiverId) {
    die("You cannot tip yourself.");
}


/*
|--------------------------------------------------------------------------
| VALIDATE AMOUNT
|--------------------------------------------------------------------------
*/

if ($amount < 100) {
    die("Minimum tip amount is ₦100.");
}


/*
|--------------------------------------------------------------------------
| CONVERT TO KOBO
|--------------------------------------------------------------------------
*/

$amountInKobo =
    $amount * 100;


/*
|--------------------------------------------------------------------------
| GENERATE UNIQUE REFERENCE
|--------------------------------------------------------------------------
*/

$reference =
    "MORESEEN_"
    . time()
    . "_"
    . bin2hex(
        random_bytes(4)
    );


/*
|--------------------------------------------------------------------------
| SAVE PENDING TIP
|--------------------------------------------------------------------------
*/

$insert = $pdo->prepare("
    INSERT INTO tips
    (
        sender_id,
        receiver_id,
        recommendation_id,
        amount,
        reference,
        status
    )

    VALUES (?, ?, ?, ?, ?, 'pending')
");

$insert->execute([
    $senderId,
    $receiverId,
    $recommendationId,
    $amount,
    $reference
]);


/*
|--------------------------------------------------------------------------
| PAYSTACK SECRET KEY
|--------------------------------------------------------------------------
*/

$secretKey =
    $_ENV["PAYSTACK_SECRET_KEY"];


if (!$secretKey) {
    die("Paystack is not configured.");
}


/*
|--------------------------------------------------------------------------
| INITIALIZE PAYSTACK
|--------------------------------------------------------------------------
*/

$url =
    "https://api.paystack.co/transaction/initialize";


$data = [

    "email" =>
        $_SESSION["user_email"],

    "amount" =>
        $amountInKobo,

    "reference" =>
        $reference,

    "callback_url" =>
        APP_URL
        . "/controllers/PaystackCallback.php"

];


$ch =
    curl_init($url);


curl_setopt(
    $ch,
    CURLOPT_POST,
    true
);


curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
);


curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "Authorization: Bearer "
        . $secretKey,

        "Content-Type: application/json"
    ]
);


curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);


$response =
    curl_exec($ch);


if ($response === false) {

    curl_close($ch);

    /*
    |----------------------------------------------------------------------
    | Mark Pending Tip As Failed
    |----------------------------------------------------------------------
    */

    $update = $pdo->prepare("
        UPDATE tips
        SET status = 'failed'
        WHERE reference = ?
    ");

    $update->execute([
        $reference
    ]);

    die("Could not connect to Paystack.");

}


curl_close($ch);


/*
|--------------------------------------------------------------------------
| DECODE PAYSTACK RESPONSE
|--------------------------------------------------------------------------
*/

$result =
    json_decode(
        $response,
        true
    );


/*
|--------------------------------------------------------------------------
| CHECK RESPONSE
|--------------------------------------------------------------------------
*/

if (
    !isset($result["status"]) ||
    $result["status"] !== true ||
    empty(
        $result["data"]["authorization_url"]
    )
) {

    $update = $pdo->prepare("
        UPDATE tips
        SET status = 'failed'
        WHERE reference = ?
    ");

    $update->execute([
        $reference
    ]);

    die("Could not initialize Paystack payment.");

}


/*
|--------------------------------------------------------------------------
| REDIRECT TO PAYSTACK
|--------------------------------------------------------------------------
*/

header(
    "Location: "
    . $result["data"]["authorization_url"]
);

exit;


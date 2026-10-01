
<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/dbconnect.php";

/*
|--------------------------------------------------------------------------
| Get Paystack Reference
|--------------------------------------------------------------------------
*/

$reference = trim($_GET["reference"] ?? "");

if ($reference === "") {
    die("Payment reference is missing.");
}

/*
|--------------------------------------------------------------------------
| Get Pending Tip
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare(
    "SELECT *
     FROM tips
     WHERE reference = ?
     LIMIT 1"
);

$query->execute([$reference]);

$tip = $query->fetch();

if (!$tip) {
    die("Tip payment was not found.");
}

/*
|--------------------------------------------------------------------------
| Verify Payment With Paystack
|--------------------------------------------------------------------------
*/

$secretKey = $_ENV["PAYSTACK_SECRET_KEY"];

$url = "https://api.paystack.co/transaction/verify/"
     . urlencode($reference);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $secretKey,
    "Content-Type: application/json"
]);

$response = curl_exec($ch);

if ($response === false) {
    curl_close($ch);
    die("Could not connect to Paystack.");
}

curl_close($ch);

$result = json_decode($response, true);

/*
|--------------------------------------------------------------------------
| Check Paystack Response
|--------------------------------------------------------------------------
*/

if (
    !isset($result["status"]) ||
    $result["status"] !== true ||
    empty($result["data"])
) {
    die("Could not verify payment.");
}

$payment = $result["data"];

/*
|--------------------------------------------------------------------------
| Check Payment Status
|--------------------------------------------------------------------------
*/

if (($payment["status"] ?? "") !== "success") {

    $update = $pdo->prepare(
        "UPDATE tips
         SET status = 'failed'
         WHERE reference = ?"
    );

    $update->execute([$reference]);

    die("Payment was not successful.");
}

/*
|--------------------------------------------------------------------------
| Check Amount
|--------------------------------------------------------------------------
*/

$expectedAmount = (int) $tip["amount"] * 100;
$paidAmount = (int) ($payment["amount"] ?? 0);

if ($paidAmount !== $expectedAmount) {

    $update = $pdo->prepare(
        "UPDATE tips
         SET status = 'failed'
         WHERE reference = ?"
    );

    $update->execute([$reference]);

    die("Payment amount could not be verified.");
}

/*
|--------------------------------------------------------------------------
| Mark Tip As Successful
|--------------------------------------------------------------------------
*/

$update = $pdo->prepare(
    "UPDATE tips
     SET status = 'success'
     WHERE reference = ?"
);

$update->execute([$reference]);

/*
|--------------------------------------------------------------------------
| Payment Successful
|--------------------------------------------------------------------------
*/

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Tip Successful - Moreseen</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            background: #1c1c1c;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        h1 {
            margin-bottom: 10px;
        }

        p {
            color: #bbb;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #fff;
            color: #111;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="box">

    <h1>🎉 Tip Successful!</h1>

    <p>
        Your tip has been successfully sent.
    </p>

    <a href="../views/recommendations/feed.php">
        Back to Recommendations
    </a>

</div>

</body>

</html>
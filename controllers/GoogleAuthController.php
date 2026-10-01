

<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/dbconnect.php";

use Google\Client;
use Firebase\JWT\JWT;

/*
|--------------------------------------------------------------------------
| Allow a small amount of clock skew
|--------------------------------------------------------------------------
*/

JWT::$leeway = 60;

/*
|--------------------------------------------------------------------------
| Google Client Setup
|--------------------------------------------------------------------------
*/

$client = new Client();

$client->setClientId($_ENV["GOOGLE_CLIENT_ID"]);
$client->setClientSecret($_ENV["GOOGLE_CLIENT_SECRET"]);
$client->setRedirectUri($_ENV["GOOGLE_REDIRECT_URI"]);

/*
|--------------------------------------------------------------------------
| Google Login Permissions
|--------------------------------------------------------------------------
*/

$client->addScope("openid");
$client->addScope("email");
$client->addScope("profile");

$client->setPrompt("select_account");


/*
|--------------------------------------------------------------------------
| Start Google Login
|--------------------------------------------------------------------------
*/

if (!isset($_GET["code"])) {

    $authUrl = $client->createAuthUrl();

    header("Location: " . $authUrl);
    exit;
}

/*
|--------------------------------------------------------------------------
| Google Sent Back an Authorization Code
|--------------------------------------------------------------------------
*/

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);

/*
|--------------------------------------------------------------------------
| Check for Errors
|--------------------------------------------------------------------------
*/

if (isset($token["error"])) {
    die("Google login failed. Please try again.");
}

$client->setAccessToken($token);

/*
|--------------------------------------------------------------------------
| Verify Google ID Token
|--------------------------------------------------------------------------
*/

$googleUser = $client->verifyIdToken();

if (!$googleUser) {
    die("Could not verify your Google account.");
}

/*
|--------------------------------------------------------------------------
| Get Google User Information
|--------------------------------------------------------------------------
*/

$googleId = $googleUser["sub"] ?? "";
$email = $googleUser["email"] ?? "";
$name = $googleUser["name"] ?? "Google User";
$avatar = $googleUser["picture"] ?? null;
$emailVerified = $googleUser["email_verified"] ?? false;

if (
    $googleId === "" ||
    $email === "" ||
    !$emailVerified
) {
    die("Your Google account could not be verified.");
}

/*
|--------------------------------------------------------------------------
| Check if Google Account Already Exists
|--------------------------------------------------------------------------
*/

$query = $pdo->prepare(
    "SELECT * FROM users WHERE google_id = ? LIMIT 1"
);

$query->execute([$googleId]);

$user = $query->fetch();

/*
|--------------------------------------------------------------------------
| If Google Account Doesn't Exist,
| Check Existing Email
|--------------------------------------------------------------------------
*/

if (!$user) {

    $query = $pdo->prepare(
        "SELECT * FROM users WHERE email = ? LIMIT 1"
    );

    $query->execute([$email]);

    $user = $query->fetch();

    /*
    |--------------------------------------------------------------------------
    | Existing Account With Same Verified Google Email
    |--------------------------------------------------------------------------
    */

    if ($user) {

        $update = $pdo->prepare(
            "UPDATE users
             SET google_id = ?, avatar = ?
             WHERE id = ?"
        );

        $update->execute([
            $googleId,
            $avatar,
            $user["id"]
        ]);

        $user["google_id"] = $googleId;
        $user["avatar"] = $avatar;
    }
}

/*
|--------------------------------------------------------------------------
| Create New User
|--------------------------------------------------------------------------
*/

if (!$user) {

    $insert = $pdo->prepare(
        "INSERT INTO users
        (name, email, password, google_id, avatar)
        VALUES (?, ?, NULL, ?, ?)"
    );

    $insert->execute([
        $name,
        $email,
        $googleId,
        $avatar
    ]);

    $userId = $pdo->lastInsertId();

    $query = $pdo->prepare(
        "SELECT * FROM users WHERE id = ? LIMIT 1"
    );

    $query->execute([$userId]);

    $user = $query->fetch();
}

/*
|--------------------------------------------------------------------------
| Log User In
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];

if (!empty($user["avatar"])) {
    $_SESSION["user_avatar"] = $user["avatar"];
}

/*
|--------------------------------------------------------------------------
| Redirect to Profile
|--------------------------------------------------------------------------
*/

header("Location: ../views/profile/index.php");
exit;


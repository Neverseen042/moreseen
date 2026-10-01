<?php

require_once __DIR__ . "/../config/dbconnect.php";
require_once __DIR__ . "/../config/config.php";


/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "register"
) {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";


    if (
        $name === "" ||
        $email === "" ||
        $password === ""
    ) {
        die("Please fill in all required fields.");
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please enter a valid email address.");
    }


    if (strlen($password) < 6) {
        die("Password must be at least 6 characters.");
    }


    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }


    $checkUser = $pdo->prepare("
        SELECT id
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $checkUser->execute([$email]);


    if ($checkUser->fetch()) {
        die("An account with this email already exists.");
    }


    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    $insertUser = $pdo->prepare("
        INSERT INTO users (
            name,
            email,
            password
        )
        VALUES (?, ?, ?)
    ");


    $insertUser->execute([
        $name,
        $email,
        $hashedPassword
    ]);


    $userId = $pdo->lastInsertId();


    session_regenerate_id(true);


    $_SESSION["user_id"] = $userId;
    $_SESSION["user_name"] = $name;
    $_SESSION["user_email"] = $email;


    header("Location: ../views/profile/index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "login"
) {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if (
        $email === "" ||
        $password === ""
    ) {
        die("Please enter your email and password.");
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please enter a valid email address.");
    }


    $query = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $query->execute([$email]);

    $user = $query->fetch();


    if (!$user) {
        die("Invalid email or password.");
    }


    /*
     * Google accounts may not have a password.
     */

    if (empty($user["password"])) {
        die("This account uses Google login. Please continue with Google.");
    }


    if (!password_verify($password, $user["password"])) {
        die("Invalid email or password.");
    }


    session_regenerate_id(true);


    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["name"];
    $_SESSION["user_email"] = $user["email"];


    header("Location: ../views/profile/index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "GET" &&
    ($_GET["action"] ?? "") === "logout"
) {

    $_SESSION = [];


    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }


    session_destroy();


    header("Location: ../views/auth/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| INVALID REQUEST
|--------------------------------------------------------------------------
*/

die("Invalid request.");
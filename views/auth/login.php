<?php

require_once __DIR__ . "/../../config/config.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"

>

<title>Login — Moreseen</title>

<style>

* {

    box-sizing: border-box;

    margin: 0;

    padding: 0;

}


html {

    min-height: 100%;

}


body {

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 30px 18px;

    color: #fff;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        radial-gradient(
            circle at 15% 10%,
            rgba(212,175,55,0.10),
            transparent 28%
        ),
        radial-gradient(
            circle at 90% 80%,
            rgba(255,255,255,0.035),
            transparent 25%
        ),
        #090b0e;

}


/*
|--------------------------------------------------------------------------
| CONTAINER
|--------------------------------------------------------------------------
*/

.login-container {

    width: 100%;

    max-width: 450px;

}


/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
*/

.logo-wrapper {

    text-align: center;

    margin-bottom: 28px;

}


.logo {

    color: #d4af37;

    font-size: 30px;

    font-weight: 800;

    letter-spacing: 1px;

}


.logo-subtitle {

    color: #646a72;

    font-size: 11px;

    margin-top: 6px;

    letter-spacing: 1.5px;

    text-transform: uppercase;

}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.card {

    position: relative;

    overflow: hidden;

    padding: 35px;

    background:
        linear-gradient(
            145deg,
            rgba(20,23,28,0.98),
            rgba(12,14,17,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 18px;

    box-shadow:
        0 25px 70px rgba(0,0,0,0.45);

}


.card::before {

    content: "";

    position: absolute;

    top: 0;

    left: 0;

    right: 0;

    height: 2px;

    background:
        linear-gradient(
            90deg,
            transparent,
            #d4af37,
            transparent
        );

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

h1 {

    font-size: 29px;

    line-height: 1.15;

    margin-bottom: 9px;

}


.subtitle {

    color: #858a91;

    font-size: 14px;

    line-height: 1.65;

    margin-bottom: 28px;

}


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

.form-group {

    margin-bottom: 18px;

}


label {

    display: block;

    margin-bottom: 8px;

    color: #d8dadd;

    font-size: 13px;

    font-weight: 600;

}


input {

    width: 100%;

    padding: 14px 15px;

    background: #090b0e;

    border:
        1px solid #30343c;

    border-radius: 9px;

    color: #fff;

    font-size: 14px;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;

}


input::placeholder {

    color: #5f646b;

}


input:focus {

    border-color: #d4af37;

    box-shadow:
        0 0 0 3px rgba(212,175,55,0.08);

}


/*
|--------------------------------------------------------------------------
| LOGIN BUTTON
|--------------------------------------------------------------------------
*/

.login-button {

    width: 100%;

    padding: 14px;

    margin-top: 5px;

    border: none;

    border-radius: 9px;

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e4bd4d
        );

    color: #111;

    font-size: 14px;

    font-weight: 800;

    cursor: pointer;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;

}


.login-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(212,175,55,0.16);

}


.login-button:active {

    transform: translateY(0);

}


/*
|--------------------------------------------------------------------------
| GOOGLE
|--------------------------------------------------------------------------
*/

.google-divider {

    display: flex;

    align-items: center;

    gap: 12px;

    margin: 23px 0;

    color: #60656c;

    font-size: 10px;

    font-weight: 700;

    letter-spacing: 1px;

}


.google-divider::before,
.google-divider::after {

    content: "";

    flex: 1;

    height: 1px;

    background:
        rgba(255,255,255,0.09);

}


.google-button {

    width: 100%;

    min-height: 48px;

    padding: 13px 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    border:
        1px solid #30343c;

    border-radius: 9px;

    background: #fff;

    color: #222;

    font-size: 13px;

    font-weight: 700;

    text-decoration: none;

    transition:
        transform 0.2s ease,
        background 0.2s ease;

}


.google-button:hover {

    background: #f1f1f1;

    transform: translateY(-1px);

}


.google-icon {

    width: 21px;

    height: 21px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #4285f4;

    font-size: 17px;

    font-weight: 800;

}


/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

.register-link {

    text-align: center;

    margin-top: 23px;

    color: #777d85;

    font-size: 13px;

}


.register-link a {

    color: #d4af37;

    text-decoration: none;

    font-weight: 700;

}


.register-link a:hover {

    text-decoration: underline;

}


/*
|--------------------------------------------------------------------------
| BACK LINK
|--------------------------------------------------------------------------
*/

.back-link {

    display: block;

    width: fit-content;

    margin:
        20px auto 0;

    color: #666b72;

    text-decoration: none;

    font-size: 12px;

    transition: 0.2s ease;

}


.back-link:hover {

    color: #fff;

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 500px) {

    body {

        padding: 20px 15px;

    }


    .logo {

        font-size: 27px;

    }


    .card {

        padding: 27px 22px;

        border-radius: 15px;

    }


    h1 {

        font-size: 26px;

    }


    .subtitle {

        font-size: 13px;

    }

}

</style>

</head>

<body>

<div class="login-container">


<div class="logo-wrapper">

    <div class="logo">
        Moreseen
    </div>

    <div class="logo-subtitle">
        Discover • Recommend • Connect
    </div>

</div>


<div class="card">


    <h1>
        Welcome back
    </h1>


    <p class="subtitle">

        Log in to your Moreseen account
        and continue discovering.

    </p>


    <form
        method="POST"
        action="../../controllers/AuthController.php"
    >


        <input
            type="hidden"
            name="action"
            value="login"
        >


        <div class="form-group">

            <label for="email">
                Email address
            </label>


            <input
                type="email"
                id="email"
                name="email"
                placeholder="you@example.com"
                autocomplete="email"
                required
            >

        </div>


        <div class="form-group">

            <label for="password">
                Password
            </label>


            <input
                type="password"
                id="password"
                name="password"
                placeholder="Your password"
                autocomplete="current-password"
                required
            >

        </div>


        <button
            type="submit"
            class="login-button"
        >

            Log In

        </button>


    </form>


    <div class="google-divider">
        OR
    </div>


    <a
        href="../../controllers/GoogleAuthController.php"
        class="google-button"
    >

        <span class="google-icon">
            G
        </span>

        Continue with Google

    </a>


    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Create one
        </a>

    </div>


</div>


<a
    href="../media/search.php"
    class="back-link"
>

    ← Continue browsing

</a>


</div>

</body>

</html>

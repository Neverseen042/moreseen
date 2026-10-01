<?php

require_once __DIR__ . "/../../config/config.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create Account — Moreseen</title>

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
    font-family: Arial, Helvetica, sans-serif;

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

.register-container {
    width: 100%;
    max-width: 450px;
}

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

    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;

    box-shadow: 0 25px 70px rgba(0,0,0,0.45);
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

.form-group {
    margin-bottom: 17px;
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
    border: 1px solid #30343c;
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
    box-shadow: 0 0 0 3px rgba(212,175,55,0.08);
}

.register-button {
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

.register-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(212,175,55,0.16);
}

.register-button:active {
    transform: translateY(0);
}

.login-link {
    text-align: center;
    margin-top: 23px;
    color: #777d85;
    font-size: 13px;
}

.login-link a {
    color: #d4af37;
    text-decoration: none;
    font-weight: 700;
}

.login-link a:hover {
    text-decoration: underline;
}

.back-link {
    display: block;
    width: fit-content;
    margin: 20px auto 0;
    color: #666b72;
    text-decoration: none;
    font-size: 12px;
    transition: 0.2s ease;
}

.back-link:hover {
    color: #fff;
}

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

<div class="register-container">

```
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
        Create your account
    </h1>

    <p class="subtitle">
        Join Moreseen and start sharing
        what you think people should watch.
    </p>

    <form
        method="POST"
        action="../../controllers/AuthController.php"
    >

        <!-- THIS FIXES THE REGISTRATION REQUEST -->
        <input
            type="hidden"
            name="action"
            value="register"
        >

        <div class="form-group">

            <label for="name">
                Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Your name"
                autocomplete="name"
                required
            >

        </div>

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
                placeholder="At least 6 characters"
                autocomplete="new-password"
                minlength="6"
                required
            >

        </div>

        <div class="form-group">

            <label for="confirm_password">
                Confirm password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Enter your password again"
                autocomplete="new-password"
                minlength="6"
                required
            >

        </div>

        <button
            type="submit"
            class="register-button"
        >
            Create Account
        </button>

    </form>

    <div class="login-link">

        Already have an account?

        <a href="login.php">
            Log in
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


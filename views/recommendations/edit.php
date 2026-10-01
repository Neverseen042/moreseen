<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/dbconnect.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/csrf.php";
require_once __DIR__ . "/../../models/Recommendation.php";

requireLogin();


/*
|--------------------------------------------------------------------------
| GET ID
|--------------------------------------------------------------------------
*/

$id = $_GET["id"] ?? "";

if (!ctype_digit((string)$id)) {

    die("Invalid recommendation.");

}

$id = (int)$id;


/*
|--------------------------------------------------------------------------
| GET RECOMMENDATION
|--------------------------------------------------------------------------
*/

$recommendation =
    Recommendation::findById($id);

if (!$recommendation) {

    die("Recommendation not found.");

}


/*
|--------------------------------------------------------------------------
| CHECK OWNERSHIP
|--------------------------------------------------------------------------
*/

if (
    (int)$recommendation["user_id"]
    !== (int)currentUserId()
) {

    die(
        "You are not allowed to edit this recommendation."
    );

}


/*
|--------------------------------------------------------------------------
| HANDLE UPDATE
|--------------------------------------------------------------------------
*/

$error = "";

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "update"
) {

    /*
    |--------------------------------------------------------------------------
    | VERIFY CSRF
    |--------------------------------------------------------------------------
    */

    if (
        !verifyCsrfToken(
            $_POST["csrf_token"] ?? ""
        )
    ) {

        $error =
            "Security verification failed. Please refresh the page and try again.";

    } else {

        $content =
            trim($_POST["content"] ?? "");


        /*
        |--------------------------------------------------------------------------
        | VALIDATE CONTENT
        |--------------------------------------------------------------------------
        */

        if ($content === "") {

            $error =
                "Please write your recommendation.";

        } elseif (strlen($content) < 30) {

            $error =
                "Your recommendation must be at least 30 characters.";

        } elseif (strlen($content) > 5000) {

            $error =
                "Your recommendation cannot exceed 5000 characters.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            $updated =
                Recommendation::update(
                    $id,
                    currentUserId(),
                    $content
                );


            /*
            |--------------------------------------------------------------------------
            | CHECK UPDATE RESULT
            |--------------------------------------------------------------------------
            */

            if ($updated) {

                header(
                    "Location: single.php?id=" . $id
                );

                exit;

            } else {

                $error =
                    "No changes were made. Make sure you changed the recommendation text.";

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| TEXT TO DISPLAY
|--------------------------------------------------------------------------
*/

$currentContent =
    $_POST["content"]
    ?? $recommendation["content"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"

>

<title>
    Edit Recommendation | Moreseen
</title>

<style>

* {

    box-sizing: border-box;

    margin: 0;

    padding: 0;

}


html {

    scroll-behavior: smooth;

}


body {

    min-height: 100vh;

    color: #fff;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        radial-gradient(
            circle at 15% 0%,
            rgba(212,175,55,0.08),
            transparent 30%
        ),
        radial-gradient(
            circle at 85% 20%,
            rgba(255,255,255,0.035),
            transparent 25%
        ),
        #090b0e;

}


/*
|--------------------------------------------------------------------------
| NAVBAR
|--------------------------------------------------------------------------
*/

.navbar {

    height: 74px;

    padding: 0 7%;

    display: flex;

    align-items: center;

    justify-content: space-between;

    position: sticky;

    top: 0;

    z-index: 100;

    background:
        rgba(9,11,14,0.78);

    backdrop-filter:
        blur(18px);

    -webkit-backdrop-filter:
        blur(18px);

    border-bottom:
        1px solid rgba(255,255,255,0.07);

}


.logo {

    color: #d4af37;

    font-size: 26px;

    font-weight: 800;

    text-decoration: none;

    letter-spacing: 1px;

}


.nav-links {

    display: flex;

    align-items: center;

    gap: 26px;

}


.nav-links a {

    color: #999;

    text-decoration: none;

    font-size: 14px;

    transition: 0.2s ease;

}


.nav-links a:hover {

    color: #d4af37;

}


/*
|--------------------------------------------------------------------------
| CONTAINER
|--------------------------------------------------------------------------
*/

.container {

    width: 90%;

    max-width: 920px;

    margin: auto;

    padding: 55px 0 90px;

}


/*
|--------------------------------------------------------------------------
| BACK LINK
|--------------------------------------------------------------------------
*/

.back-link {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    color: #8d9299;

    text-decoration: none;

    font-size: 13px;

    margin-bottom: 30px;

    transition: 0.2s ease;

}


.back-link:hover {

    color: #d4af37;

}


/*
|--------------------------------------------------------------------------
| HEADING
|--------------------------------------------------------------------------
*/

.page-label {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    color: #d4af37;

    text-transform: uppercase;

    letter-spacing: 2.5px;

    font-size: 11px;

    font-weight: 700;

    margin-bottom: 12px;

}


.page-label::before {

    content: "";

    width: 24px;

    height: 1px;

    background: #d4af37;

}


.heading h1 {

    font-size:
        clamp(32px, 5vw, 45px);

    line-height: 1.08;

    letter-spacing: -1px;

    margin-bottom: 12px;

}


.heading p {

    color: #92969d;

    line-height: 1.7;

    font-size: 15px;

    margin-bottom: 35px;

}


/*
|--------------------------------------------------------------------------
| MEDIA CARD
|--------------------------------------------------------------------------
*/

.media-card {

    position: relative;

    overflow: hidden;

    display: flex;

    gap: 22px;

    background:
        linear-gradient(
            135deg,
            rgba(27,30,36,0.96),
            rgba(15,17,21,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 18px;

    padding: 20px;

    margin-bottom: 24px;

    box-shadow:
        0 18px 45px rgba(0,0,0,0.25);

}


.media-card::before {

    content: "";

    position: absolute;

    left: 0;

    top: 0;

    bottom: 0;

    width: 3px;

    background: #d4af37;

}


.poster {

    width: 105px;

    height: 150px;

    object-fit: cover;

    border-radius: 11px;

    flex-shrink: 0;

    box-shadow:
        0 12px 30px rgba(0,0,0,0.35);

}


.poster-placeholder {

    width: 105px;

    height: 150px;

    border-radius: 11px;

    background:
        linear-gradient(
            145deg,
            #24272d,
            #17191d
        );

    color: #686e76;

    display: flex;

    align-items: center;

    justify-content: center;

    text-align: center;

    font-size: 12px;

    flex-shrink: 0;

}


.media-info {

    display: flex;

    flex-direction: column;

    justify-content: center;

    min-width: 0;

}


.media-type {

    display: inline-block;

    width: fit-content;

    color: #d4af37;

    background:
        rgba(212,175,55,0.10);

    border:
        1px solid rgba(212,175,55,0.20);

    border-radius: 999px;

    padding: 6px 10px;

    font-size: 10px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 1px;

    margin-bottom: 11px;

}


.media-title {

    font-size:
        clamp(22px, 3vw, 28px);

    line-height: 1.2;

    margin-bottom: 10px;

}


.rating {

    color: #92969d;

    font-size: 13px;

}


/*
|--------------------------------------------------------------------------
| FORM CARD
|--------------------------------------------------------------------------
*/

.form-card {

    background:
        linear-gradient(
            145deg,
            rgba(20,23,28,0.98),
            rgba(14,16,20,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 18px;

    padding: 30px;

    box-shadow:
        0 20px 50px rgba(0,0,0,0.22);

}


.form-header {

    margin-bottom: 22px;

}


.form-title {

    font-size: 20px;

    font-weight: 700;

    margin-bottom: 7px;

}


.form-description {

    color: #777d85;

    font-size: 13px;

    line-height: 1.6;

}


label {

    display: block;

    margin-bottom: 10px;

    font-size: 14px;

    font-weight: 700;

}


textarea {

    width: 100%;

    min-height: 300px;

    resize: vertical;

    padding: 17px;

    background: #0b0d10;

    border:
        1px solid #30343c;

    border-radius: 12px;

    color: #fff;

    font-family: inherit;

    font-size: 15px;

    line-height: 1.75;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;

}


textarea::placeholder {

    color: #555b63;

}


textarea:focus {

    border-color: #d4af37;

    box-shadow:
        0 0 0 3px rgba(212,175,55,0.08);

}


.hint {

    color: #686e76;

    font-size: 12px;

    margin-top: 9px;

    line-height: 1.5;

}


/*
|--------------------------------------------------------------------------
| ERROR
|--------------------------------------------------------------------------
*/

.error {

    display: flex;

    align-items: flex-start;

    gap: 10px;

    background:
        rgba(220,70,70,0.10);

    border:
        1px solid rgba(220,70,70,0.25);

    color: #ff9b9b;

    padding: 14px 16px;

    border-radius: 11px;

    margin-bottom: 22px;

    font-size: 13px;

    line-height: 1.5;

}


.error::before {

    content: "!";

    width: 19px;

    height: 19px;

    border-radius: 50%;

    background:
        rgba(220,70,70,0.16);

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    font-weight: 800;

}


/*
|--------------------------------------------------------------------------
| ACTIONS
|--------------------------------------------------------------------------
*/

.actions {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-top: 25px;

}


.button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 46px;

    padding: 13px 23px;

    border-radius: 10px;

    text-decoration: none;

    font-weight: 800;

    border: none;

    cursor: pointer;

    font-size: 14px;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;

}


.save {

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e5bd4d
        );

    color: #111;

}


.save:hover {

    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(212,175,55,0.18);

}


.cancel {

    background:
        #292d34;

    color: #e1e3e5;

}


.cancel:hover {

    background:
        #343942;

    transform: translateY(-2px);

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 700px) {

    .navbar {

        padding: 0 5%;

    }


    .nav-links {

        gap: 15px;

    }


    .container {

        width: 92%;

        padding-top: 40px;

    }


    .media-card {

        padding: 17px;

    }


    .poster,
    .poster-placeholder {

        width: 90px;

        height: 130px;

    }


    .form-card {

        padding: 22px;

    }

}


@media (max-width: 500px) {

    .logo {

        font-size: 23px;

    }


    .nav-links a {

        font-size: 12px;

    }


    .heading h1 {

        font-size: 30px;

    }


    .media-card {

        gap: 15px;

    }


    .poster,
    .poster-placeholder {

        width: 78px;

        height: 115px;

    }


    .media-title {

        font-size: 20px;

    }


    .form-card {

        padding: 18px;

    }


    textarea {

        min-height: 260px;

    }


    .actions {

        flex-direction: column;

    }


    .button {

        width: 100%;

    }

}

</style>

</head>

<body>

<nav class="navbar">


<a
    href="../profile/index.php"
    class="logo"
>
    Moreseen
</a>


<div class="nav-links">

    <a href="../media/search.php">
        Discover
    </a>

    <a href="feed.php">
        Community
    </a>

</div>


</nav>

<div class="container">


<a
    href="../profile/recommendations.php"
    class="back-link"
>
    ← Back to My Recommendations
</a>


<div class="page-label">
    Community
</div>


<div class="heading">

    <h1>
        Edit your recommendation
    </h1>

    <p>
        Update your thoughts about this movie or TV series.
    </p>

</div>


<div class="media-card">


    <?php if (!empty($recommendation["poster_path"])): ?>

        <img
            src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars(
                $recommendation["poster_path"]
            ) ?>"
            alt="<?= htmlspecialchars(
                $recommendation["title"]
            ) ?>"
            class="poster"
            loading="lazy"
        >

    <?php else: ?>

        <div class="poster-placeholder">
            No poster available
        </div>

    <?php endif; ?>


    <div class="media-info">

        <div class="media-type">

            <?= $recommendation["media_type"] === "movie"
                ? "Movie"
                : "TV Series"
            ?>

        </div>


        <h2 class="media-title">

            <?= htmlspecialchars(
                $recommendation["title"]
            ) ?>

        </h2>


        <?php if (
            $recommendation["rating"] !== null
        ): ?>

            <div class="rating">

                ⭐
                <?= number_format(
                    (float)$recommendation["rating"],
                    1
                ) ?>/10

            </div>

        <?php endif; ?>

    </div>

</div>


<div class="form-card">


    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="form-header">

        <div class="form-title">
            Refine your recommendation
        </div>

        <div class="form-description">

            Make any changes you want to your recommendation.
            Your updated version will replace the current one.

        </div>

    </div>


    <form
        method="POST"
        action=""
    >

        <?= csrfField() ?>


        <input
            type="hidden"
            name="action"
            value="update"
        >


        <label for="content">
            Your recommendation
        </label>


        <textarea
            id="content"
            name="content"
            required
            minlength="30"
            maxlength="5000"
        ><?= htmlspecialchars(
            $currentContent
        ) ?></textarea>


        <div class="hint">

            Minimum 30 characters · Maximum 5000 characters

        </div>


        <div class="actions">


            <button
                type="submit"
                class="button save"
            >
                Save Changes
            </button>


            <a
                href="single.php?id=<?= $id ?>"
                class="button cancel"
            >
                Cancel
            </a>


        </div>


    </form>


</div>


</div>

</body>

</html>

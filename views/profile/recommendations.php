<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/dbconnect.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/csrf.php";
require_once __DIR__ . "/../../models/Recommendation.php";

requireLogin();

$userId = currentUserId();

$message = "";


/*
|--------------------------------------------------------------------------
| DELETE RECOMMENDATION
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "delete"
) {

    $csrfToken =
        $_POST["csrf_token"] ?? "";

    if (!verifyCsrfToken($csrfToken)) {

        $message =
            "Security verification failed. Please refresh the page and try again.";

    } else {

        $recommendationId =
            $_POST["recommendation_id"] ?? "";

        if (!ctype_digit((string)$recommendationId)) {

            $message =
                "Invalid recommendation.";

        } else {

            $deleted =
                Recommendation::delete(
                    (int)$recommendationId,
                    $userId
                );

            if ($deleted) {

                $message =
                    "Recommendation deleted successfully.";

            } else {

                $message =
                    "Unable to delete this recommendation. It may not belong to you or may no longer exist.";

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| GET USER RECOMMENDATIONS
|--------------------------------------------------------------------------
*/

$recommendations =
    Recommendation::getByUser($userId);

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
    My Recommendations | Moreseen
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
            circle at 10% 0%,
            rgba(212,175,55,0.08),
            transparent 28%
        ),
        radial-gradient(
            circle at 90% 15%,
            rgba(255,255,255,0.035),
            transparent 24%
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

    letter-spacing: 1px;

    text-decoration: none;

}


.nav-links {

    display: flex;

    align-items: center;

    gap: 25px;

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

    max-width: 1150px;

    margin: auto;

    padding: 60px 0 90px;

}


/*
|--------------------------------------------------------------------------
| PAGE HEADER
|--------------------------------------------------------------------------
*/

.page-label {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    color: #d4af37;

    font-size: 11px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 2.5px;

    margin-bottom: 12px;

}


.page-label::before {

    content: "";

    width: 25px;

    height: 1px;

    background: #d4af37;

}


h1 {

    font-size:
        clamp(34px, 5vw, 46px);

    line-height: 1.08;

    letter-spacing: -1px;

    margin-bottom: 12px;

}


.subtitle {

    color: #92969d;

    margin-bottom: 36px;

    line-height: 1.7;

    font-size: 15px;

}


/*
|--------------------------------------------------------------------------
| MESSAGE
|--------------------------------------------------------------------------
*/

.message {

    display: flex;

    align-items: center;

    gap: 10px;

    background:
        rgba(212,175,55,0.08);

    border:
        1px solid rgba(212,175,55,0.22);

    color: #d4af37;

    padding: 14px 17px;

    border-radius: 11px;

    margin-bottom: 25px;

    font-size: 13px;

    line-height: 1.5;

}


.message::before {

    content: "✓";

    width: 20px;

    height: 20px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background:
        rgba(212,175,55,0.12);

    flex-shrink: 0;

}


/*
|--------------------------------------------------------------------------
| GRID
|--------------------------------------------------------------------------
*/

.grid {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(240px, 1fr)
        );

    gap: 25px;

}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.card {

    position: relative;

    overflow: hidden;

    background:
        linear-gradient(
            145deg,
            rgba(20,23,28,0.98),
            rgba(13,15,18,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.07);

    border-radius: 17px;

    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;

}


.card::after {

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

    opacity: 0;

    transition: 0.25s ease;

}


.card:hover {

    transform: translateY(-6px);

    border-color:
        rgba(212,175,55,0.30);

    box-shadow:
        0 18px 45px rgba(0,0,0,0.35);

}


.card:hover::after {

    opacity: 1;

}


/*
|--------------------------------------------------------------------------
| POSTER
|--------------------------------------------------------------------------
*/

.poster,
.poster-placeholder {

    width: 100%;

    height: 330px;

    object-fit: cover;

    display: block;

}


.poster {

    transition:
        transform 0.35s ease;

}


.card:hover .poster {

    transform: scale(1.025);

}


.poster-placeholder {

    background:
        linear-gradient(
            145deg,
            #25282d,
            #15171b
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: #686e76;

    font-size: 13px;

}


/*
|--------------------------------------------------------------------------
| CARD CONTENT
|--------------------------------------------------------------------------
*/

.card-content {

    padding: 19px;

}


.media-type {

    display: inline-block;

    color: #d4af37;

    background:
        rgba(212,175,55,0.09);

    border:
        1px solid rgba(212,175,55,0.18);

    border-radius: 999px;

    padding: 5px 9px;

    font-size: 9px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 1px;

    margin-bottom: 11px;

}


.title {

    font-size: 20px;

    line-height: 1.3;

    margin-bottom: 11px;

}


.text {

    color: #92969d;

    font-size: 13px;

    line-height: 1.65;

    display: -webkit-box;

    -webkit-line-clamp: 4;

    -webkit-box-orient: vertical;

    overflow: hidden;

    margin-bottom: 15px;

}


.date {

    color: #626870;

    font-size: 11px;

    margin-bottom: 18px;

}


/*
|--------------------------------------------------------------------------
| ACTIONS
|--------------------------------------------------------------------------
*/

.actions {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 7px;

}


.button {

    min-height: 37px;

    display: flex;

    align-items: center;

    justify-content: center;

    text-align: center;

    text-decoration: none;

    padding: 9px 6px;

    border-radius: 8px;

    font-size: 11px;

    font-weight: 700;

    transition:
        transform 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease;

}


.button:hover {

    transform: translateY(-1px);

}


.view {

    background:
        rgba(212,175,55,0.09);

    border:
        1px solid rgba(212,175,55,0.24);

    color: #d4af37;

}


.view:hover {

    background:
        rgba(212,175,55,0.15);

}


.edit {

    background:
        #22262d;

    border:
        1px solid #30343c;

    color: #ddd;

}


.edit:hover {

    background:
        #2c3139;

}


.delete-form {

    margin: 0;

}


.delete-button {

    width: 100%;

    min-height: 37px;

    padding: 9px 6px;

    border-radius: 8px;

    border:
        1px solid rgba(220,80,80,0.22);

    background:
        rgba(220,80,80,0.07);

    color: #e58c8c;

    font-size: 11px;

    font-weight: 700;

    cursor: pointer;

    transition:
        transform 0.2s ease,
        background 0.2s ease;

}


.delete-button:hover {

    background:
        rgba(220,80,80,0.14);

    transform: translateY(-1px);

}


/*
|--------------------------------------------------------------------------
| EMPTY STATE
|--------------------------------------------------------------------------
*/

.empty {

    position: relative;

    overflow: hidden;

    text-align: center;

    background:
        linear-gradient(
            145deg,
            rgba(20,23,28,0.98),
            rgba(13,15,18,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 18px;

    padding: 80px 25px;

}


.empty::before {

    content: "";

    position: absolute;

    top: 0;

    left: 50%;

    transform: translateX(-50%);

    width: 90px;

    height: 2px;

    background: #d4af37;

}


.empty-icon {

    width: 55px;

    height: 55px;

    margin:
        0 auto 18px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #d4af37;

    font-size: 25px;

    background:
        rgba(212,175,55,0.09);

    border:
        1px solid rgba(212,175,55,0.20);

}


.empty h2 {

    font-size: 23px;

    margin-bottom: 10px;

}


.empty p {

    color: #858a91;

    line-height: 1.7;

    max-width: 520px;

    margin:
        0 auto 26px;

}


.create-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e5bd4d
        );

    color: #111;

    text-decoration: none;

    padding: 13px 21px;

    border-radius: 9px;

    font-size: 13px;

    font-weight: 800;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;

}


.create-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(212,175,55,0.16);

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

        gap: 14px;

    }


    .nav-links a {

        font-size: 12px;

    }


    .container {

        width: 92%;

        padding-top: 45px;

    }


    .grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 14px;

    }


    .poster,
    .poster-placeholder {

        height: 270px;

    }


    .card-content {

        padding: 14px;

    }


    .title {

        font-size: 17px;

    }


    .text {

        font-size: 12px;

    }


    .actions {

        grid-template-columns: 1fr;

    }


    .button,
    .delete-button {

        min-height: 38px;

    }

}


@media (max-width: 480px) {

    .logo {

        font-size: 23px;

    }


    .nav-links a {

        display: none;

    }


    h1 {

        font-size: 31px;

    }


    .grid {

        grid-template-columns: 1fr;

        gap: 20px;

    }


    .poster,
    .poster-placeholder {

        height: 380px;

    }


    .card-content {

        padding: 18px;

    }


    .actions {

        grid-template-columns:
            repeat(3, 1fr);

    }

}

</style>

</head>

<body>

<nav class="navbar">


<a
    href="index.php"
    class="logo"
>
    Moreseen
</a>


<div class="nav-links">

    <a href="../media/search.php">
        Discover
    </a>

    <a href="../recommendations/feed.php">
        Community
    </a>

    <a href="index.php">
        Profile
    </a>

</div>


</nav>

<main class="container">


<div class="page-label">
    Your Content
</div>


<h1>
    My Recommendations
</h1>


<p class="subtitle">

    Everything you've recommended to the
    Moreseen community.

</p>


<?php if ($message !== ""): ?>

    <div class="message">

        <?= htmlspecialchars($message) ?>

    </div>

<?php endif; ?>


<?php if (empty($recommendations)): ?>


    <div class="empty">


        <div class="empty-icon">
            +
        </div>


        <h2>
            You haven't recommended anything yet.
        </h2>


        <p>

            Found a movie or TV show you think
            people need to see?

        </p>


        <a
            href="../media/search.php"
            class="create-button"
        >

            Find Something to Recommend

        </a>


    </div>


<?php else: ?>


    <div class="grid">


        <?php foreach (
            $recommendations
            as $recommendation
        ): ?>


            <?php

            $title =
                $recommendation["title"]
                ?? "Untitled";


            $poster =
                $recommendation["poster_path"]
                ?? null;


            $mediaType =
                $recommendation["media_type"]
                ?? "movie";


            $content =
                $recommendation["content"]
                ?? "";


            $createdAt =
                $recommendation["created_at"]
                ?? "";


            $recommendationId =
                $recommendation["id"];


            $date =
                $createdAt !== ""
                    ? date(
                        "M j, Y",
                        strtotime($createdAt)
                    )
                    : "";

            ?>


            <article class="card">


                <?php if ($poster): ?>

                    <img
                        class="poster"
                        src="<?= TMDB_IMAGE_URL
                            . htmlspecialchars($poster) ?>"
                        alt="<?= htmlspecialchars($title) ?>"
                        loading="lazy"
                    >

                <?php else: ?>

                    <div class="poster-placeholder">
                        No poster
                    </div>

                <?php endif; ?>


                <div class="card-content">


                    <div class="media-type">

                        <?= $mediaType === "movie"
                            ? "Movie"
                            : "TV Series"
                        ?>

                    </div>


                    <h2 class="title">

                        <?= htmlspecialchars($title) ?>

                    </h2>


                    <p class="text">

                        <?= htmlspecialchars($content) ?>

                    </p>


                    <div class="date">

                        Published
                        <?= htmlspecialchars($date) ?>

                    </div>


                    <div class="actions">


                        <a
                            href="../recommendations/single.php?id=<?= urlencode($recommendationId) ?>"
                            class="button view"
                        >
                            View
                        </a>


                        <a
                            href="../recommendations/edit.php?id=<?= urlencode($recommendationId) ?>"
                            class="button edit"
                        >
                            Edit
                        </a>


                        <form
                            method="POST"
                            action=""
                            class="delete-form"
                        >

                            <?= csrfField() ?>


                            <input
                                type="hidden"
                                name="action"
                                value="delete"
                            >


                            <input
                                type="hidden"
                                name="recommendation_id"
                                value="<?= (int)$recommendationId ?>"
                            >


                            <button
                                type="submit"
                                class="delete-button"
                            >
                                Delete
                            </button>

                        </form>


                    </div>


                </div>

            </article>


        <?php endforeach; ?>


    </div>


<?php endif; ?>


</main>

</body>

</html>

<?php

require_once __DIR__ . "/../../helpers/csrf.php";
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/dbconnect.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../models/Recommendation.php";

requireLogin();


/*
|--------------------------------------------------------------------------
| GET RECOMMENDATION ID
|--------------------------------------------------------------------------
*/

$recommendationId =
    $_GET["id"] ?? "";


if (!ctype_digit((string)$recommendationId)) {

    die("Invalid recommendation.");

}


/*
|--------------------------------------------------------------------------
| GET RECOMMENDATION
|--------------------------------------------------------------------------
*/

$recommendation =
    Recommendation::findById(
        (int)$recommendationId
    );


if (!$recommendation) {

    die("Recommendation not found.");

}


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$title =
    $recommendation["title"]
    ?? "Untitled";


$content =
    $recommendation["content"]
    ?? "";


$userName =
    $recommendation["user_name"]
    ?? "Moreseen User";


$userAvatar =
    $recommendation["user_avatar"]
    ?? null;


$mediaType =
    $recommendation["media_type"]
    ?? "movie";


$tmdbId =
    $recommendation["tmdb_id"]
    ?? 0;


$poster =
    $recommendation["poster_path"]
    ?? null;


$backdrop =
    $recommendation["backdrop_path"]
    ?? null;


$overview =
    $recommendation["overview"]
    ?? "";


$rating =
    $recommendation["rating"]
    ?? null;


$createdAt =
    $recommendation["created_at"]
    ?? "";


/*
|--------------------------------------------------------------------------
| COMMUNITY COUNT
|--------------------------------------------------------------------------
*/

$countQuery = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM recommendations
    WHERE media_id = ?
");

$countQuery->execute([
    $recommendation["media_id"]
]);

$communityCount =
    $countQuery->fetch()["total"]
    ?? 0;


/*
|--------------------------------------------------------------------------
| POST DATE
|--------------------------------------------------------------------------
*/

$formattedDate =
    $createdAt !== ""
        ? date(
            "F j, Y",
            strtotime($createdAt)
        )
        : "";


/*
|--------------------------------------------------------------------------
| BACKDROP
|--------------------------------------------------------------------------
*/

$backdropUrl =
    $backdrop
        ? "https://image.tmdb.org/t/p/original"
            . $backdrop
        : "";


/*
|--------------------------------------------------------------------------
| POSTER
|--------------------------------------------------------------------------
*/

$posterUrl =
    $poster
        ? TMDB_IMAGE_URL . $poster
        : "";


/*
|--------------------------------------------------------------------------
| AUTHOR INITIAL
|--------------------------------------------------------------------------
*/

$authorInitial =
    strtoupper(
        substr(
            $userName,
            0,
            1
        )
    );

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
    <?= htmlspecialchars($title) ?> Recommendation | Moreseen
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
        #090b0e;

}


/*
|--------------------------------------------------------------------------
| NAVBAR
|--------------------------------------------------------------------------
*/

.navbar {

    position: absolute;

    top: 0;

    left: 0;

    width: 100%;

    height: 74px;

    padding: 0 7%;

    display: flex;

    align-items: center;

    justify-content: space-between;

    z-index: 20;

    background:
        linear-gradient(
            to bottom,
            rgba(5,7,9,0.72),
            transparent
        );

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

    color: #c6c8cc;

    text-decoration: none;

    font-size: 13px;

    transition: 0.2s ease;

}


.nav-links a:hover {

    color: #d4af37;

}


/*
|--------------------------------------------------------------------------
| HERO
|--------------------------------------------------------------------------
*/

.hero {

    min-height: 620px;

    position: relative;

    display: flex;

    align-items: center;

    overflow: hidden;

    background-color: #090b0e;

}


.hero::before {

    content: "";

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            90deg,
            rgba(7,9,12,0.98) 3%,
            rgba(7,9,12,0.91) 35%,
            rgba(7,9,12,0.56) 70%,
            rgba(7,9,12,0.82) 100%
        ),
        linear-gradient(
            to top,
            #090b0e 0%,
            transparent 42%
        );

    z-index: 1;

}


<?php if ($backdropUrl): ?>

.hero::after {

    content: "";

    position: absolute;

    inset: 0;

    background-image:
        url("<?= htmlspecialchars($backdropUrl) ?>");

    background-size: cover;

    background-position: center;

    z-index: 0;

    opacity: 0.8;

}

<?php endif; ?>


.hero-content {

    position: relative;

    z-index: 2;

    width: 86%;

    max-width: 1200px;

    margin: auto;

    padding-top: 75px;

    display: flex;

    align-items: center;

    gap: 48px;

}


/*
|--------------------------------------------------------------------------
| POSTER
|--------------------------------------------------------------------------
*/

.poster {

    width: 270px;

    min-height: 400px;

    flex-shrink: 0;

    border-radius: 16px;

    overflow: hidden;

    background:
        linear-gradient(
            145deg,
            #25282d,
            #14161a
        );

    border:
        1px solid rgba(255,255,255,0.08);

    box-shadow:
        0 30px 80px rgba(0,0,0,0.6);

}


.poster img {

    width: 100%;

    min-height: 400px;

    object-fit: cover;

    display: block;

}


/*
|--------------------------------------------------------------------------
| HERO INFO
|--------------------------------------------------------------------------
*/

.info {

    max-width: 720px;

}


.media-type {

    display: inline-flex;

    align-items: center;

    color: #111;

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e4bd4d
        );

    padding: 7px 12px;

    border-radius: 999px;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1px;

    text-transform: uppercase;

    margin-bottom: 16px;

}


h1 {

    font-size:
        clamp(38px, 5vw, 58px);

    line-height: 1.02;

    letter-spacing: -1.5px;

    margin-bottom: 15px;

}


.meta {

    display: flex;

    align-items: center;

    gap: 15px;

    min-height: 25px;

    margin-bottom: 20px;

}


.rating {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    color: #f5c542;

    font-size: 14px;

    font-weight: 700;

}


.overview {

    max-width: 680px;

    color: #c5c8cc;

    line-height: 1.75;

    font-size: 15px;

}


/*
|--------------------------------------------------------------------------
| MAIN
|--------------------------------------------------------------------------
*/

.main {

    width: 86%;

    max-width: 950px;

    margin: auto;

    padding: 50px 0 95px;

}


/*
|--------------------------------------------------------------------------
| AUTHOR
|--------------------------------------------------------------------------
*/

.author-card {

    display: flex;

    align-items: center;

    gap: 15px;

    padding-bottom: 25px;

    border-bottom:
        1px solid rgba(255,255,255,0.07);

    margin-bottom: 34px;

}


.avatar {

    width: 56px;

    height: 56px;

    flex-shrink: 0;

    border-radius: 50%;

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #8f7220
        );

    color: #111;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: 800;

    font-size: 20px;

    overflow: hidden;

    border:
        2px solid rgba(212,175,55,0.25);

}


.avatar img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


.author-name {

    font-size: 16px;

    font-weight: 700;

    margin-bottom: 5px;

}


.author-date {

    color: #70757d;

    font-size: 12px;

}


/*
|--------------------------------------------------------------------------
| RECOMMENDATION
|--------------------------------------------------------------------------
*/

.recommendation-title {

    position: relative;

    font-size: 27px;

    line-height: 1.2;

    margin-bottom: 20px;

    padding-left: 15px;

}


.recommendation-title::before {

    content: "";

    position: absolute;

    left: 0;

    top: 2px;

    bottom: 2px;

    width: 3px;

    border-radius: 5px;

    background: #d4af37;

}


.recommendation-text {

    color: #d1d3d6;

    font-size: 17px;

    line-height: 1.9;

    white-space: pre-line;

}


/*
|--------------------------------------------------------------------------
| COMMUNITY BOX
|--------------------------------------------------------------------------
*/

.community-box {

    margin-top: 48px;

    padding: 25px 28px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 25px;

    background:
        linear-gradient(
            135deg,
            rgba(23,26,31,0.98),
            rgba(14,16,20,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.08);

    border-left:
        3px solid #d4af37;

    border-radius: 14px;

}


.community-title {

    font-size: 17px;

    font-weight: 700;

    margin-bottom: 7px;

}


.community-description {

    max-width: 600px;

    color: #858a91;

    font-size: 13px;

    line-height: 1.6;

}


.community-number {

    color: #d4af37;

    font-size: 34px;

    font-weight: 800;

    text-align: right;

}


.community-label {

    color: #666c74;

    font-size: 11px;

    text-align: right;

    text-transform: uppercase;

    letter-spacing: 0.8px;

}


/*
|--------------------------------------------------------------------------
| TIP CREATOR
|--------------------------------------------------------------------------
*/

.tip-box {

    margin-top: 25px;

    padding: 25px;

    background:
        linear-gradient(
            135deg,
            rgba(23,26,31,0.98),
            rgba(14,16,20,0.98)
        );

    border:
        1px solid rgba(255,255,255,0.08);

    border-radius: 14px;

    box-shadow:
        0 12px 35px rgba(0,0,0,0.18);

}


.tip-title {

    font-size: 19px;

    font-weight: 700;

    margin-bottom: 8px;

}


.tip-description {

    color: #92969d;

    font-size: 13px;

    line-height: 1.65;

    margin-bottom: 18px;

}


.tip-form {

    display: flex;

    align-items: center;

    gap: 10px;

}


.tip-input {

    flex: 1;

    min-width: 0;

    padding: 13px 15px;

    border:
        1px solid #30343c;

    border-radius: 9px;

    background: #090b0e;

    color: #fff;

    font-size: 14px;

    outline: none;

    transition: 0.2s ease;

}


.tip-input:focus {

    border-color: #d4af37;

    box-shadow:
        0 0 0 3px rgba(212,175,55,0.08);

}


.tip-input::placeholder {

    color: #62666c;

}


.tip-button {

    padding: 13px 21px;

    border: none;

    border-radius: 9px;

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e4bd4d
        );

    color: #111;

    font-size: 13px;

    font-weight: 800;

    cursor: pointer;

    white-space: nowrap;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;

}


.tip-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 8px 20px rgba(212,175,55,0.14);

}


.tip-note {

    margin-top: 10px;

    color: #62666c;

    font-size: 11px;

}


/*
|--------------------------------------------------------------------------
| ACTIONS
|--------------------------------------------------------------------------
*/

.actions {

    display: flex;

    gap: 10px;

    margin-top: 30px;

    flex-wrap: wrap;

}


.button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-height: 43px;

    padding: 11px 19px;

    border-radius: 9px;

    text-decoration: none;

    font-weight: 700;

    font-size: 12px;

    transition:
        transform 0.2s ease,
        background 0.2s ease;

}


.primary {

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e4bd4d
        );

    color: #111;

}


.secondary {

    background: #181b20;

    color: #ddd;

    border:
        1px solid #30343c;

}


.button:hover {

    transform: translateY(-2px);

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 750px) {

    .navbar {

        padding: 0 5%;

    }


    .nav-links {

        gap: 15px;

    }


    .nav-links a {

        display: none;

    }


    .hero {

        min-height: auto;

        padding: 105px 0 65px;

    }


    .hero::before {

        background:
            linear-gradient(
                to bottom,
                rgba(7,9,12,0.72),
                rgba(7,9,12,0.95)
            );

    }


    .hero-content {

        width: 92%;

        flex-direction: column;

        align-items: flex-start;

        gap: 28px;

        padding-top: 20px;

    }


    .poster {

        width: 190px;

        min-height: 280px;

    }


    .poster img {

        min-height: 280px;

    }


    h1 {

        font-size: 36px;

    }


    .overview {

        font-size: 14px;

    }


    .main {

        width: 92%;

        padding-top: 42px;

    }


    .recommendation-title {

        font-size: 24px;

    }


    .recommendation-text {

        font-size: 15px;

        line-height: 1.8;

    }


    .community-box {

        align-items: flex-start;

        flex-direction: column;

        padding: 22px;

    }


    .community-number,
    .community-label {

        text-align: left;

    }


    .tip-box {

        padding: 19px;

        border-radius: 13px;

    }


    .tip-form {

        flex-direction: column;

        align-items: stretch;

    }


    .tip-input,
    .tip-button {

        width: 100%;

    }


    .tip-button {

        padding: 14px;

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

<!-- NAVBAR -->

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

<!-- HERO -->

<section class="hero">


<div class="hero-content">


    <div class="poster">

        <?php if ($posterUrl): ?>

            <img
                src="<?= htmlspecialchars($posterUrl) ?>"
                alt="<?= htmlspecialchars($title) ?>"
                loading="eager"
            >

        <?php else: ?>

            <div
                style="
                    min-height:400px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    color:#666;
                    font-size:13px;
                "
            >
                No poster available
            </div>

        <?php endif; ?>

    </div>


    <div class="info">


        <span class="media-type">

            <?= $mediaType === "movie"
                ? "Movie"
                : "TV Series"
            ?>

        </span>


        <h1>

            <?= htmlspecialchars($title) ?>

        </h1>


        <div class="meta">

            <?php if ($rating !== null): ?>

                <span class="rating">

                    ★
                    <?= number_format(
                        (float)$rating,
                        1
                    ) ?>/10

                </span>

            <?php endif; ?>

        </div>


        <?php if ($overview !== ""): ?>

            <p class="overview">

                <?= htmlspecialchars(
                    $overview
                ) ?>

            </p>

        <?php endif; ?>


    </div>


</div>
```

</section>

<!-- MAIN -->

<main class="main">


<!-- AUTHOR -->

<div class="author-card">


    <div class="avatar">

        <?php if ($userAvatar): ?>

            <img
                src="<?= htmlspecialchars(
                    $userAvatar
                ) ?>"
                alt=""
                loading="lazy"
            >

        <?php else: ?>

            <?= htmlspecialchars(
                $authorInitial
            ) ?>

        <?php endif; ?>

    </div>


    <div>

        <div class="author-name">

            <?= htmlspecialchars(
                $userName
            ) ?>

        </div>


        <div class="author-date">

            Recommended on
            <?= htmlspecialchars(
                $formattedDate
            ) ?>

        </div>

    </div>


</div>


<!-- RECOMMENDATION -->

<h2 class="recommendation-title">

    Why you should watch this

</h2>


<div class="recommendation-text">

    <?= htmlspecialchars(
        $content
    ) ?>

</div>


<!-- COMMUNITY -->

<div class="community-box">


    <div>

        <div class="community-title">

            Moreseen Community

        </div>


        <div class="community-description">

            This title has been recommended by
            other members of the Moreseen community.

        </div>

    </div>


    <div>

        <div class="community-number">

            <?= number_format(
                (int)$communityCount
            ) ?>

        </div>


        <div class="community-label">

            recommendations

        </div>

    </div>


</div>


<!-- TIP CREATOR -->

<?php if (
    (int)$recommendation["user_id"]
    !==
    (int)$_SESSION["user_id"]
): ?>

    <div class="tip-box">


        <div class="tip-title">

            Support <?= htmlspecialchars(
                $userName
            ) ?> ☕

        </div>


        <div class="tip-description">

            Enjoyed this recommendation?
            Send the creator a small tip
            to show your appreciation.

        </div>


        <form
            method="POST"
            action="../../controllers/PaystackController.php"
            class="tip-form"
        >

            <?= csrfField() ?>


            <input
                type="hidden"
                name="recommendation_id"
                value="<?= (int)$recommendationId ?>"
            >


            <input
                type="number"
                name="amount"
                class="tip-input"
                placeholder="Enter amount (₦)"
                min="100"
                step="100"
                required
            >


            <button
                type="submit"
                class="tip-button"
            >

                Tip Creator

            </button>

        </form>


        <div class="tip-note">

            Minimum tip: ₦100

        </div>


    </div>

<?php endif; ?>


<!-- ACTIONS -->

<div class="actions">


    <a
        href="../media/details.php?id=<?= urlencode(
            $tmdbId
        ) ?>&type=<?= urlencode(
            $mediaType
        ) ?>"
        class="button primary"
    >

        View <?= $mediaType === "movie"
            ? "Movie"
            : "TV Series"
        ?>

    </a>


    <a
        href="feed.php"
        class="button secondary"
    >

        ← Back to Community

    </a>


</div>


</main>

</body>

</html>

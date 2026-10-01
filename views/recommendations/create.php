<?php

require_once __DIR__ . "/../../helpers/csrf.php";
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/tmdb.php";
require_once __DIR__ . "/../../config/dbconnect.php";
require_once __DIR__ . "/../../helpers/auth.php";

require_once __DIR__ . "/../../models/Media.php";
require_once __DIR__ . "/../../models/Recommendation.php";

requireLogin();

$message = "";
$messageType = "";

$selectedMedia = null;


/*
|--------------------------------------------------------------------------
| GET SELECTED MEDIA
|--------------------------------------------------------------------------
*/

$tmdbId = trim($_GET["id"] ?? "");
$mediaType = trim($_GET["type"] ?? "");

if (
    $tmdbId !== "" &&
    in_array($mediaType, ["movie", "tv"])
) {

    $url = $tmdbBaseUrl
        . "/"
        . $mediaType
        . "/"
        . urlencode($tmdbId)
        . "?api_key="
        . urlencode($tmdbApiKey);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    curl_close($ch);

    if ($response !== false) {

        $data = json_decode($response, true);

        if (
            isset($data["id"]) &&
            !isset($data["status_code"])
        ) {

            $selectedMedia = $data;

        }
    }
}


/*
|--------------------------------------------------------------------------
| SUBMIT RECOMMENDATION
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "recommend"
) {

    /*
    |----------------------------------------------------------------------
    | VERIFY CSRF
    |----------------------------------------------------------------------
    */

    if (
        !verifyCsrfToken(
            $_POST["csrf_token"] ?? ""
        )
    ) {

        $message =
            "Invalid or expired security token. Please try again.";

        $messageType = "error";

    } else {

        /*
        |------------------------------------------------------------------
        | GET POST DATA
        |------------------------------------------------------------------
        */

        $tmdbId = trim(
            $_POST["tmdb_id"] ?? ""
        );

        $mediaType = trim(
            $_POST["media_type"] ?? ""
        );

        $content = trim(
            $_POST["content"] ?? ""
        );


        /*
        |------------------------------------------------------------------
        | VALIDATION
        |------------------------------------------------------------------
        */

        if (
            $tmdbId === "" ||
            !in_array(
                $mediaType,
                ["movie", "tv"]
            )
        ) {

            $message =
                "Please select a valid movie or TV show.";

            $messageType = "error";

        } elseif (strlen($content) < 30) {

            $message =
                "Your recommendation should be at least 30 characters.";

            $messageType = "error";

        } elseif (strlen($content) > 5000) {

            $message =
                "Your recommendation is too long. Please keep it under 5000 characters.";

            $messageType = "error";

        } else {

            /*
            |------------------------------------------------------------------
            | GET MEDIA FROM TMDb
            |------------------------------------------------------------------
            */

            $url = $tmdbBaseUrl
                . "/"
                . $mediaType
                . "/"
                . urlencode($tmdbId)
                . "?api_key="
                . urlencode($tmdbApiKey);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);

            curl_close($ch);

            if ($response === false) {

                $message =
                    "Unable to connect to TMDb.";

                $messageType = "error";

            } else {

                $data = json_decode(
                    $response,
                    true
                );

                if (
                    !isset($data["id"]) ||
                    isset($data["status_code"])
                ) {

                    $message =
                        "The selected movie or TV show could not be found.";

                    $messageType = "error";

                } else {

                    /*
                    |------------------------------------------------------------------
                    | GET TITLE
                    |------------------------------------------------------------------
                    */

                    $title =
                        $data["title"]
                        ?? $data["name"]
                        ?? "Untitled";


                    /*
                    |------------------------------------------------------------------
                    | FIND OR CREATE MEDIA
                    |------------------------------------------------------------------
                    */

                    $mediaId =
                        Media::findOrCreate([

                            "tmdb_id" =>
                                $data["id"],

                            "media_type" =>
                                $mediaType,

                            "title" =>
                                $title,

                            "poster_path" =>
                                $data["poster_path"]
                                ?? null,

                            "backdrop_path" =>
                                $data["backdrop_path"]
                                ?? null,

                            "overview" =>
                                $data["overview"]
                                ?? null,

                            "release_date" =>
                                $data["release_date"]
                                ?? $data["first_air_date"]
                                ?? null,

                            "rating" =>
                                $data["vote_average"]
                                ?? null

                        ]);


                    /*
                    |------------------------------------------------------------------
                    | CHECK DUPLICATE
                    |------------------------------------------------------------------
                    */

                    if (
                        Recommendation::exists(
                            currentUserId(),
                            $mediaId
                        )
                    ) {

                        $message =
                            "You have already recommended this title.";

                        $messageType = "error";

                    } else {

                        /*
                        |------------------------------------------------------------------
                        | CREATE RECOMMENDATION
                        |------------------------------------------------------------------
                        */

                        Recommendation::create(
                            currentUserId(),
                            $mediaId,
                            $content
                        );


                        /*
                        |------------------------------------------------------------------
                        | SUCCESS
                        |------------------------------------------------------------------
                        */

                        header(
                            "Location: feed.php"
                        );

                        exit;
                    }
                }
            }
        }
    }
}

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
    Write Recommendation | Moreseen
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

    color: #fff;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    min-height: 100vh;

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

    padding: 65px 0 90px;

}


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


h1 {

    font-size: clamp(32px, 5vw, 46px);

    line-height: 1.08;

    letter-spacing: -1px;

    margin-bottom: 12px;

}


.subtitle {

    color: #92969d;

    line-height: 1.7;

    max-width: 650px;

    margin-bottom: 38px;

    font-size: 15px;

}


/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/

.alert {

    padding: 16px 18px;

    border-radius: 12px;

    margin-bottom: 25px;

    font-size: 14px;

    line-height: 1.5;

}


.alert-error {

    background:
        rgba(220,70,70,0.10);

    border:
        1px solid rgba(220,80,80,0.25);

    color: #ff9b9b;

}


/*
|--------------------------------------------------------------------------
| MEDIA CARD
|--------------------------------------------------------------------------
*/

.media-card {

    position: relative;

    overflow: hidden;

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

    display: flex;

    gap: 24px;

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


.media-poster {

    width: 125px;

    height: 185px;

    object-fit: cover;

    border-radius: 11px;

    flex-shrink: 0;

    box-shadow:
        0 12px 30px rgba(0,0,0,0.35);

}


.poster-placeholder {

    width: 125px;

    height: 185px;

    border-radius: 11px;

    background:
        linear-gradient(
            145deg,
            #24272d,
            #17191d
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: #777;

    text-align: center;

    font-size: 13px;

    flex-shrink: 0;

}


.media-info {

    padding-top: 5px;

    min-width: 0;

}


.media-type {

    display: inline-block;

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

    margin-bottom: 13px;

}


.media-title {

    font-size: clamp(23px, 3vw, 29px);

    line-height: 1.2;

    margin-bottom: 11px;

}


.media-overview {

    color: #92969d;

    font-size: 14px;

    line-height: 1.7;

    display: -webkit-box;

    -webkit-line-clamp: 5;

    -webkit-box-orient: vertical;

    overflow: hidden;

}


/*
|--------------------------------------------------------------------------
| FORM CARD
|--------------------------------------------------------------------------
*/

.form-card {

    position: relative;

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

    margin-bottom: 7px;

}


.form-description {

    color: #777d85;

    font-size: 13px;

    line-height: 1.6;

}


.form-label {

    display: block;

    font-size: 14px;

    font-weight: 700;

    margin-bottom: 10px;

}


.textarea-wrapper {

    position: relative;

}


.textarea {

    width: 100%;

    min-height: 280px;

    resize: vertical;

    background:
        #0b0d10;

    border:
        1px solid #30343c;

    border-radius: 12px;

    color: #fff;

    padding: 17px;

    font-family: inherit;

    font-size: 15px;

    line-height: 1.75;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;

}


.textarea::placeholder {

    color: #555b63;

}


.textarea:focus {

    border-color: #d4af37;

    box-shadow:
        0 0 0 3px rgba(212,175,55,0.08);

}


.form-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-top: 10px;

}


.help-text {

    color: #686e76;

    font-size: 12px;

    line-height: 1.5;

}


.submit-button {

    margin-top: 20px;

    border: none;

    border-radius: 10px;

    padding: 14px 22px;

    background:
        linear-gradient(
            135deg,
            #d4af37,
            #e5bd4d
        );

    color: #111;

    font-weight: 800;

    font-size: 14px;

    cursor: pointer;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        filter 0.2s ease;

}


.submit-button:hover {

    transform: translateY(-2px);

    box-shadow:
        0 10px 25px rgba(212,175,55,0.18);

    filter: brightness(1.06);

}


.submit-button:active {

    transform: translateY(0);

}


/*
|--------------------------------------------------------------------------
| EMPTY STATE
|--------------------------------------------------------------------------
*/

.empty-state {

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

    padding: 75px 25px;

}


.empty-state::before {

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

    width: 54px;

    height: 54px;

    margin: 0 auto 18px;

    border-radius: 50%;

    background:
        rgba(212,175,55,0.10);

    border:
        1px solid rgba(212,175,55,0.20);

    display: flex;

    align-items: center;

    justify-content: center;

    color: #d4af37;

    font-size: 22px;

}


.empty-state h2 {

    font-size: 23px;

    margin-bottom: 10px;

}


.empty-state p {

    color: #858a91;

    line-height: 1.7;

    max-width: 520px;

    margin:
        0 auto 27px;

}


.search-button {

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

    font-weight: 800;

    font-size: 14px;

    transition: 0.2s ease;

}


.search-button:hover {

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


    .container {

        width: 92%;

        padding-top: 45px;

    }


    .media-card {

        flex-direction: column;

        align-items: flex-start;

    }


    .media-poster,
    .poster-placeholder {

        width: 145px;

        height: 215px;

    }


    .form-card {

        padding: 22px;

    }


    .textarea {

        min-height: 250px;

    }

}


@media (max-width: 480px) {

    .logo {

        font-size: 23px;

    }


    .nav-links a {

        font-size: 12px;

    }


    h1 {

        font-size: 30px;

    }


    .subtitle {

        font-size: 14px;

    }


    .media-card {

        padding: 16px;

    }


    .form-card {

        padding: 18px;

    }


    .form-footer {

        display: block;

    }


    .submit-button {

        width: 100%;

    }


    .empty-state {

        padding:
            60px 18px;

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

<main class="container">


<div class="page-label">
    Community
</div>


<h1>
    Recommend something.
</h1>


<p class="subtitle">

    Tell the Moreseen community what they should
    watch and, more importantly, why.

</p>


<?php if ($message !== ""): ?>

    <div class="alert alert-error">

        <?= htmlspecialchars($message) ?>

    </div>

<?php endif; ?>


<?php if ($selectedMedia): ?>


    <?php

    $selectedTitle =
        $selectedMedia["title"]
        ?? $selectedMedia["name"]
        ?? "Untitled";


    $selectedPoster =
        $selectedMedia["poster_path"]
        ?? null;


    $selectedOverview =
        $selectedMedia["overview"]
        ?? "No overview available.";

    ?>


    <!-- SELECTED MEDIA -->

    <div class="media-card">


        <?php if ($selectedPoster): ?>

            <img
                class="media-poster"
                src="<?= TMDB_IMAGE_URL
                    . htmlspecialchars($selectedPoster) ?>"
                alt="<?= htmlspecialchars($selectedTitle) ?>"
                loading="lazy"
            >

        <?php else: ?>

            <div class="poster-placeholder">
                No poster available
            </div>

        <?php endif; ?>


        <div class="media-info">

            <div class="media-type">

                <?= $mediaType === "movie"
                    ? "Movie"
                    : "TV Series"
                ?>

            </div>


            <h2 class="media-title">

                <?= htmlspecialchars(
                    $selectedTitle
                ) ?>

            </h2>


            <p class="media-overview">

                <?= htmlspecialchars(
                    $selectedOverview
                ) ?>

            </p>

        </div>

    </div>


    <!-- RECOMMENDATION FORM -->

    <form
        method="POST"
        class="form-card"
    >

        <?= csrfField() ?>


        <input
            type="hidden"
            name="action"
            value="recommend"
        >


        <input
            type="hidden"
            name="tmdb_id"
            value="<?= htmlspecialchars(
                $tmdbId
            ) ?>"
        >


        <input
            type="hidden"
            name="media_type"
            value="<?= htmlspecialchars(
                $mediaType
            ) ?>"
        >


        <div class="form-header">

            <div class="form-title">
                Why should people watch it?
            </div>

            <div class="form-description">

                Share your honest thoughts with the community.
                Talk about what stood out to you without giving
                away major spoilers.

            </div>

        </div>


        <label
            class="form-label"
            for="content"
        >

            Your recommendation

        </label>


        <div class="textarea-wrapper">

            <textarea
                id="content"
                name="content"
                class="textarea"
                placeholder="What makes this worth watching? Talk about the story, characters, performances, emotions, or anything else that stood out to you..."
                required
                minlength="30"
                maxlength="5000"
            ></textarea>

        </div>


        <div class="form-footer">

            <div class="help-text">

                Minimum 30 characters • Maximum 5000 characters

            </div>

        </div>


        <button
            type="submit"
            class="submit-button"
        >

            Publish Recommendation

        </button>


    </form>


<?php else: ?>


    <!-- NOTHING SELECTED -->

    <div class="empty-state">


        <div class="empty-icon">
            +
        </div>


        <h2>
            Choose something to recommend
        </h2>


        <p>

            Search for a movie or TV show first,
            then come back here to write your recommendation.

        </p>


        <a
            href="../media/search.php"
            class="search-button"
        >

            Search Movies & TV

        </a>


    </div>


<?php endif; ?>


</main>

</body>

</html>

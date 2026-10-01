
<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/dbconnect.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../models/Recommendation.php";

requireLogin();


/*
|--------------------------------------------------------------------------
| GET COMMUNITY RECOMMENDATIONS
|--------------------------------------------------------------------------
*/

$recommendations =
    Recommendation::getFeed();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Community | Moreseen</title>


<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}


body {

    background:
        radial-gradient(
            circle at 15% 0%,
            rgba(212,175,55,0.08),
            transparent 28%
        ),
        radial-gradient(
            circle at 90% 20%,
            rgba(212,175,55,0.05),
            transparent 25%
        ),
        #0b0d10;

    color: white;

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

    height: 75px;

    padding: 0 7%;

    display: flex;

    align-items: center;

    justify-content: space-between;

    border-bottom:
        1px solid rgba(255,255,255,0.08);

    background:
        rgba(11,13,16,0.96);

    position: sticky;

    top: 0;

    z-index: 100;

    backdrop-filter: blur(12px);
}


.logo {

    color: #d4af37;

    font-size: 27px;

    font-weight: bold;

    letter-spacing: 1px;

    text-decoration: none;

    transition: 0.2s;
}


.logo:hover {

    opacity: 0.85;
}


.nav-links {

    display: flex;

    align-items: center;

    gap: 22px;
}


.nav-links a {

    color: #aaa;

    text-decoration: none;

    font-size: 14px;

    transition: 0.2s;
}


.nav-links a:hover {

    color: #d4af37;
}


.logout {

    border:
        1px solid rgba(255,255,255,0.15);

    padding: 8px 14px;

    border-radius: 8px;

    transition: 0.2s !important;
}


.logout:hover {

    border-color:
        rgba(212,175,55,0.45);

    background:
        rgba(212,175,55,0.06);
}


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

.container {

    width: 90%;

    max-width: 1050px;

    margin: auto;

    padding: 60px 0 90px;
}


.page-label {

    color: #d4af37;

    text-transform: uppercase;

    font-size: 11px;

    font-weight: bold;

    letter-spacing: 2.5px;

    margin-bottom: 12px;
}


.page-title {

    font-size: 44px;

    line-height: 1.1;

    letter-spacing: -1px;

    margin-bottom: 12px;
}


.page-subtitle {

    color: #929292;

    line-height: 1.7;

    max-width: 680px;

    font-size: 15px;

    margin-bottom: 45px;
}


/*
|--------------------------------------------------------------------------
| FEED
|--------------------------------------------------------------------------
*/

.feed {

    display: flex;

    flex-direction: column;

    gap: 25px;
}


.recommendation {

    background:
        linear-gradient(
            135deg,
            rgba(25,28,34,0.98),
            rgba(16,18,23,0.98)
        );

    border:
        1px solid #282c34;

    border-radius: 18px;

    padding: 26px;

    transition:
        transform 0.25s ease,
        border-color 0.25s ease,
        box-shadow 0.25s ease;

    overflow: hidden;

    position: relative;
}


.recommendation::before {

    content: "";

    position: absolute;

    top: 0;

    left: 0;

    width: 3px;

    height: 0;

    background: #d4af37;

    transition: height 0.25s ease;
}


.recommendation:hover {

    border-color:
        rgba(212,175,55,0.3);

    box-shadow:
        0 20px 50px rgba(0,0,0,0.32);

    transform: translateY(-3px);
}


.recommendation:hover::before {

    height: 100%;
}


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

.user {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 22px;
}


.avatar {

    width: 44px;

    height: 44px;

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

    font-weight: bold;

    font-size: 16px;

    overflow: hidden;

    flex-shrink: 0;
}


.avatar img {

    width: 100%;

    height: 100%;

    object-fit: cover;
}


.user-name {

    font-size: 15px;

    font-weight: bold;

    margin-bottom: 4px;
}


.post-date {

    color: #777;

    font-size: 12px;
}


/*
|--------------------------------------------------------------------------
| MEDIA
|--------------------------------------------------------------------------
*/

.media {

    display: flex;

    gap: 22px;

    margin-bottom: 22px;

    align-items: flex-start;
}


.poster {

    width: 135px;

    height: 195px;

    object-fit: cover;

    border-radius: 11px;

    flex-shrink: 0;

    background:
        #20242b;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #777;

    font-size: 12px;

    text-align: center;

    overflow: hidden;

    box-shadow:
        0 10px 25px rgba(0,0,0,0.25);
}


.poster img {

    width: 100%;

    height: 100%;

    object-fit: cover;
}


.media-info {

    padding-top: 4px;

    min-width: 0;
}


.media-type {

    display: inline-block;

    color: #d4af37;

    text-transform: uppercase;

    font-size: 10px;

    font-weight: bold;

    letter-spacing: 1.3px;

    margin-bottom: 9px;

    padding: 5px 9px;

    border-radius: 20px;

    background:
        rgba(212,175,55,0.08);

    border:
        1px solid rgba(212,175,55,0.18);
}


.media-title {

    font-size: 25px;

    line-height: 1.2;

    margin-bottom: 9px;

    word-break: break-word;
}


.rating {

    color: #f5c542;

    font-size: 14px;

    font-weight: bold;

    margin-bottom: 12px;
}


/*
|--------------------------------------------------------------------------
| RECOMMENDATION TEXT
|--------------------------------------------------------------------------
*/

.content {

    color: #d0d0d0;

    font-size: 15px;

    line-height: 1.8;

    white-space: pre-line;

    margin-bottom: 22px;

    max-width: 900px;
}


/*
|--------------------------------------------------------------------------
| FOOTER
|--------------------------------------------------------------------------
*/

.card-footer {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    border-top:
        1px solid rgba(255,255,255,0.07);

    padding-top: 18px;
}


.community-count {

    color: #888;

    font-size: 13px;
}


.community-count strong {

    color: #d4af37;

    font-size: 16px;

    margin: 0 2px;
}


.view-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    text-decoration: none;

    background:
        rgba(212,175,55,0.08);

    border:
        1px solid rgba(212,175,55,0.3);

    color: #d4af37;

    padding: 10px 16px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: bold;

    transition: 0.2s;

    white-space: nowrap;
}


.view-button:hover {

    background: #d4af37;

    color: #111;

    transform: translateY(-1px);
}


/*
|--------------------------------------------------------------------------
| EMPTY
|--------------------------------------------------------------------------
*/

.empty {

    text-align: center;

    background:
        linear-gradient(
            135deg,
            #171a1f,
            #111318
        );

    border:
        1px solid #282c34;

    border-radius: 18px;

    padding: 80px 25px;

    box-shadow:
        0 15px 40px rgba(0,0,0,0.2);
}


.empty-icon {

    width: 60px;

    height: 60px;

    margin:
        0 auto 20px;

    border-radius: 50%;

    background:
        rgba(212,175,55,0.08);

    border:
        1px solid rgba(212,175,55,0.2);

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;
}


.empty h2 {

    font-size: 25px;

    margin-bottom: 10px;
}


.empty p {

    color: #888;

    line-height: 1.6;

    max-width: 500px;

    margin:
        0 auto 25px;
}


.empty-button {

    display: inline-block;

    background: #d4af37;

    color: #111;

    text-decoration: none;

    padding: 13px 20px;

    border-radius: 8px;

    font-weight: bold;

    transition: 0.2s;
}


.empty-button:hover {

    background: #e0bc4c;

    transform: translateY(-2px);
}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 650px) {

    .navbar {

        height: 68px;

        padding: 0 5%;
    }


    .logo {

        font-size: 23px;
    }


    .nav-links {

        gap: 7px;
    }


    .nav-links a:not(.logout) {

        display: none;
    }


    .logout {

        padding: 7px 11px;

        font-size: 12px !important;
    }


    .container {

        width: 92%;

        padding:
            40px 0 70px;
    }


    .page-label {

        font-size: 10px;

        letter-spacing: 2px;
    }


    .page-title {

        font-size: 32px;

        letter-spacing: -0.5px;
    }


    .page-subtitle {

        font-size: 14px;

        margin-bottom: 32px;
    }


    .recommendation {

        padding: 20px;

        border-radius: 15px;
    }


    .user {

        margin-bottom: 18px;
    }


    .media {

        gap: 15px;

        margin-bottom: 20px;
    }


    .poster {

        width: 105px;

        height: 155px;

        border-radius: 9px;
    }


    .media-title {

        font-size: 19px;

        line-height: 1.25;
    }


    .media-type {

        font-size: 9px;

        padding: 4px 7px;
    }


    .rating {

        font-size: 13px;
    }


    .content {

        font-size: 14px;

        line-height: 1.75;
    }


    .card-footer {

        align-items: stretch;

        flex-direction: column;

        gap: 14px;
    }


    .community-count {

        font-size: 12px;
    }


    .view-button {

        width: 100%;

        padding: 12px;
    }


    .empty {

        padding: 60px 20px;
    }


    .empty h2 {

        font-size: 21px;
    }

}


/*
|--------------------------------------------------------------------------
| VERY SMALL SCREENS
|--------------------------------------------------------------------------
*/

@media (max-width: 400px) {

    .media {

        gap: 12px;
    }


    .poster {

        width: 90px;

        height: 135px;
    }


    .media-title {

        font-size: 17px;
    }


    .recommendation {

        padding: 17px;
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


        <a href="../profile/index.php">
            Profile
        </a>


        <a
            href="../../controllers/AuthController.php?action=logout"
            class="logout"
        >
            Logout
        </a>

    </div>

</nav>


<!-- PAGE -->

<main class="container">


    <div class="page-label">

        Moreseen Community

    </div>


    <h1 class="page-title">

        What are people watching?

    </h1>


    <p class="page-subtitle">

        Discover movies and TV shows recommended by
        people in the Moreseen community. Find something
        interesting, then share your own take.

    </p>


    <?php if (empty($recommendations)): ?>


        <div class="empty">

            <div class="empty-icon">
                🎬
            </div>


            <h2>

                The community is just getting started.

            </h2>


            <p>

                Be one of the first people to recommend
                something worth watching.

            </p>


            <a
                href="../media/search.php"
                class="empty-button"
            >

                Discover Something

            </a>

        </div>


    <?php else: ?>


        <div class="feed">


            <?php foreach ($recommendations as $recommendation): ?>


                <?php

                $userName =
                    $recommendation["user_name"]
                    ?? "Moreseen User";


                $title =
                    $recommendation["title"]
                    ?? "Untitled";


                $poster =
                    $recommendation["poster_path"]
                    ?? null;


                $rating =
                    $recommendation["rating"]
                    ?? null;


                $content =
                    $recommendation["content"]
                    ?? "";


                $mediaType =
                    $recommendation["media_type"]
                    ?? "movie";


                $tmdbId =
                    $recommendation["tmdb_id"]
                    ?? 0;


                $userAvatar =
                    $recommendation["user_avatar"]
                    ?? null;


                $createdAt =
                    $recommendation["created_at"]
                    ?? "";


                $recommendationId =
                    $recommendation["id"];


                /*
                |--------------------------------------------------------------------------
                | GET COMMUNITY COUNT
                |--------------------------------------------------------------------------
                */

                $countQuery = $pdo->prepare("
                    SELECT COUNT(*) AS total
                    FROM recommendations
                    WHERE media_id = (
                        SELECT media_id
                        FROM recommendations
                        WHERE id = ?
                    )
                ");

                $countQuery->execute([
                    $recommendationId
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
                            "M j, Y",
                            strtotime($createdAt)
                        )
                        : "";

                ?>


                <article class="recommendation">


                    <!-- USER -->

                    <div class="user">


                        <div class="avatar">

                            <?php if ($userAvatar): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $userAvatar
                                    ) ?>"
                                    alt=""
                                >

                            <?php else: ?>

                                <?= htmlspecialchars(
                                    strtoupper(
                                        substr(
                                            $userName,
                                            0,
                                            1
                                        )
                                    )
                                ) ?>

                            <?php endif; ?>

                        </div>


                        <div>

                            <div class="user-name">

                                <?= htmlspecialchars(
                                    $userName
                                ) ?>

                            </div>


                            <div class="post-date">

                                Recommended on
                                <?= htmlspecialchars(
                                    $formattedDate
                                ) ?>

                            </div>

                        </div>


                    </div>


                    <!-- MEDIA -->

                    <div class="media">


                        <?php if ($poster): ?>

                            <img
                                class="poster"
                                src="<?= TMDB_IMAGE_URL
                                    . htmlspecialchars($poster) ?>"
                                alt="<?= htmlspecialchars($title) ?>"
                            >

                        <?php else: ?>

                            <div class="poster">

                                No poster

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
                                    $title
                                ) ?>

                            </h2>


                            <?php if ($rating !== null): ?>

                                <div class="rating">

                                    ★
                                    <?= number_format(
                                        (float)$rating,
                                        1
                                    ) ?>/10

                                </div>

                            <?php endif; ?>


                        </div>


                    </div>


                    <!-- RECOMMENDATION -->

                    <div class="content">

                        <?= htmlspecialchars(
                            $content
                        ) ?>

                    </div>


                    <!-- FOOTER -->

                    <div class="card-footer">


                        <div class="community-count">

                            🔥

                            <strong>
                                <?= number_format(
                                    (int)$communityCount
                                ) ?>
                            </strong>

                            people recommended this

                        </div>


                        <a
                            href="single.php?id=<?= urlencode(
                                $recommendationId
                            ) ?>"
                            class="view-button"
                        >

                            Read Full Recommendation →

                        </a>


                    </div>


                </article>


            <?php endforeach; ?>


        </div>


    <?php endif; ?>


</main>


</body>

</html>




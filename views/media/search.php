
<?php

require_once __DIR__ . "/../../config/tmdb.php";
require_once __DIR__ . "/../../config/config.php";

$searchQuery = trim($_GET["q"] ?? "");

$results = [];

if ($searchQuery !== "") {

    $url = $tmdbBaseUrl
        . "/search/multi?api_key="
        . urlencode($tmdbApiKey)
        . "&query="
        . urlencode($searchQuery)
        . "&include_adult=false";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    curl_close($ch);

    if ($response !== false) {

        $data = json_decode($response, true);

        $results = $data["results"] ?? [];
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

    <title>Discover | Moreseen</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

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
                    circle at 95% 30%,
                    rgba(212,175,55,0.05),
                    transparent 25%
                ),
                #0b0d10;

            color: white;

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
                rgba(11,13,16,0.95);

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


        .nav-right {

            display: flex;

            align-items: center;

            gap: 20px;
        }


        .nav-link {

            color: #aaa;

            text-decoration: none;

            font-size: 14px;

            transition: 0.2s;
        }


        .nav-link:hover {

            color: #d4af37;
        }


        .nav-link.active {

            color: #d4af37;

            font-weight: bold;
        }


        .logout {

            border:
                1px solid rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 8px;

            color: white;
        }


        .logout:hover {

            border-color: #d4af37;

            color: #d4af37;
        }


        /*
        |--------------------------------------------------------------------------
        | MAIN
        |--------------------------------------------------------------------------
        */

        .container {

            width: 88%;

            max-width: 1250px;

            margin: auto;

            padding: 65px 0 90px;
        }


        .hero {

            max-width: 760px;

            margin-bottom: 35px;
        }


        .eyebrow {

            color: #d4af37;

            text-transform: uppercase;

            font-size: 11px;

            font-weight: bold;

            letter-spacing: 2.5px;

            margin-bottom: 12px;
        }


        .search-title {

            font-size: 46px;

            line-height: 1.1;

            letter-spacing: -1.5px;

            margin-bottom: 13px;
        }


        .search-subtitle {

            color: #929292;

            font-size: 15px;

            line-height: 1.7;

            max-width: 650px;
        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH FORM
        |--------------------------------------------------------------------------
        */

        .search-form {

            display: flex;

            gap: 12px;

            margin-bottom: 48px;

            padding: 7px;

            background:
                rgba(20,23,28,0.9);

            border:
                1px solid #292d35;

            border-radius: 13px;

            box-shadow:
                0 15px 40px rgba(0,0,0,0.2);
        }


        .search-input {

            flex: 1;

            min-width: 0;

            padding: 15px 17px;

            border: none;

            background: transparent;

            color: white;

            font-size: 15px;

            outline: none;
        }


        .search-input::placeholder {

            color: #666;
        }


        .search-input:focus {

            outline: none;
        }


        .search-button {

            padding: 0 27px;

            min-height: 48px;

            border: none;

            border-radius: 9px;

            background: #d4af37;

            color: #111;

            font-weight: bold;

            font-size: 14px;

            cursor: pointer;

            transition: 0.2s;
        }


        .search-button:hover {

            background: #e8c45a;

            transform: translateY(-1px);
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTS HEADING
        |--------------------------------------------------------------------------
        */

        .results-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin-bottom: 22px;
        }


        .results-heading {

            font-size: 23px;

            line-height: 1.3;
        }


        .results-count {

            color: #777;

            font-size: 13px;

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTS GRID
        |--------------------------------------------------------------------------
        */

        .results-grid {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(180px, 1fr)
                );

            gap: 22px;
        }


        /*
        |--------------------------------------------------------------------------
        | MOVIE CARD
        |--------------------------------------------------------------------------
        */

        .movie-card {

            display: block;

            text-decoration: none;

            color: white;

            background:
                linear-gradient(
                    145deg,
                    #171a1f,
                    #111318
                );

            border:
                1px solid #272b32;

            border-radius: 14px;

            overflow: hidden;

            transition:
                transform 0.25s ease,
                border-color 0.25s ease,
                box-shadow 0.25s ease;

            position: relative;
        }


        .movie-card::after {

            content: "";

            position: absolute;

            left: 0;

            right: 0;

            bottom: 0;

            height: 3px;

            background: #d4af37;

            transform: scaleX(0);

            transform-origin: center;

            transition: 0.25s ease;
        }


        .movie-card:hover {

            transform: translateY(-7px);

            border-color:
                rgba(212,175,55,0.4);

            box-shadow:
                0 18px 40px rgba(0,0,0,0.4);
        }


        .movie-card:hover::after {

            transform: scaleX(1);
        }


        /*
        |--------------------------------------------------------------------------
        | POSTER
        |--------------------------------------------------------------------------
        */

        .poster-wrapper {

            position: relative;

            height: 275px;

            overflow: hidden;

            background: #202329;
        }


        .poster {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;

            transition:
                transform 0.35s ease;
        }


        .movie-card:hover .poster {

            transform: scale(1.05);
        }


        .poster-overlay {

            position: absolute;

            inset: 0;

            background:
                linear-gradient(
                    to top,
                    rgba(0,0,0,0.65),
                    transparent 45%
                );

            pointer-events: none;
        }


        .type-badge {

            position: absolute;

            top: 12px;

            left: 12px;

            padding: 5px 8px;

            border-radius: 6px;

            background:
                rgba(10,11,13,0.82);

            border:
                1px solid rgba(255,255,255,0.12);

            color: #d4af37;

            font-size: 10px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: 0.7px;

            backdrop-filter: blur(6px);
        }


        .rating-badge {

            position: absolute;

            right: 12px;

            bottom: 12px;

            padding: 5px 8px;

            border-radius: 6px;

            background:
                rgba(10,11,13,0.85);

            color: #eee;

            font-size: 11px;

            font-weight: bold;

            backdrop-filter: blur(6px);
        }


        .poster-placeholder {

            height: 275px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-direction: column;

            gap: 8px;

            background:
                linear-gradient(
                    135deg,
                    #202329,
                    #17191d
                );

            color: #777;

            font-size: 13px;
        }


        .placeholder-icon {

            font-size: 28px;

            opacity: 0.5;
        }


        /*
        |--------------------------------------------------------------------------
        | MOVIE INFO
        |--------------------------------------------------------------------------
        */

        .movie-info {

            padding: 16px;
        }


        .movie-title {

            font-size: 16px;

            line-height: 1.35;

            margin-bottom: 8px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        .movie-type {

            color: #888;

            font-size: 11px;

            text-transform: uppercase;

            letter-spacing: 0.8px;
        }


        .rating {

            color: #aaa;

            margin-top: 9px;

            font-size: 13px;
        }


        .rating span {

            color: #d4af37;
        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .empty {

            text-align: center;

            padding: 75px 20px;

            background:
                rgba(20,23,28,0.65);

            border:
                1px solid #252930;

            border-radius: 16px;

            color: #888;
        }


        .empty-icon {

            font-size: 38px;

            margin-bottom: 15px;

            opacity: 0.7;
        }


        .empty h2 {

            color: #ddd;

            font-size: 20px;

            margin-bottom: 8px;
        }


        .empty p {

            color: #777;

            font-size: 14px;

            line-height: 1.6;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .navbar {

                height: 68px;

                padding: 0 5%;
            }


            .logo {

                font-size: 23px;
            }


            .nav-right {

                gap: 8px;
            }


            .nav-link {

                display: none;
            }


            .logout {

                display: block;

                padding: 7px 11px;

                font-size: 12px;
            }


            .container {

                width: 92%;

                padding: 42px 0 70px;
            }


            .search-title {

                font-size: 33px;

                letter-spacing: -0.8px;
            }


            .search-subtitle {

                font-size: 14px;
            }


            .search-form {

                flex-direction: column;

                padding: 7px;

                gap: 5px;

                margin-bottom: 35px;
            }


            .search-input {

                padding: 14px 13px;
            }


            .search-button {

                width: 100%;

                height: 48px;
            }


            .results-top {

                align-items: flex-start;

                flex-direction: column;

                gap: 5px;
            }


            .results-heading {

                font-size: 20px;
            }


            .results-grid {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

                gap: 13px;
            }


            .poster-wrapper {

                height: 245px;
            }


            .poster-placeholder {

                height: 245px;
            }


            .movie-info {

                padding: 12px;
            }


            .movie-title {

                font-size: 14px;
            }


            .movie-type {

                font-size: 10px;
            }


            .rating {

                font-size: 12px;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | SMALL PHONES
        |--------------------------------------------------------------------------
        */

        @media (max-width: 380px) {

            .results-grid {

                gap: 10px;
            }


            .poster-wrapper {

                height: 215px;
            }


            .poster-placeholder {

                height: 215px;
            }


            .movie-info {

                padding: 10px;
            }


            .movie-title {

                font-size: 13px;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar">

    <a
        href="search.php"
        class="logo"
    >
        Moreseen
    </a>


    <div class="nav-right">

        <a
            href="search.php"
            class="nav-link active"
        >
            Discover
        </a>


        <a
            href="../recommendations/feed.php"
            class="nav-link"
        >
            Community
        </a>


        <a
            href="../profile/index.php"
            class="nav-link"
        >
            Profile
        </a>


        <a
            href="../../controllers/AuthController.php?action=logout"
            class="nav-link logout"
        >
            Logout
        </a>

    </div>

</nav>


<!-- MAIN -->

<main class="container">


    <!-- HERO -->

    <section class="hero">

        <div class="eyebrow">
            Discover
        </div>


        <h1 class="search-title">
            Find something to watch.
        </h1>


        <p class="search-subtitle">

            Search movies and TV shows and discover what
            the Moreseen community is talking about.

        </p>

    </section>


    <!-- SEARCH FORM -->

    <form
        method="GET"
        class="search-form"
    >

        <input
            type="text"
            name="q"
            class="search-input"
            placeholder="Search movies or TV shows..."
            value="<?= htmlspecialchars($searchQuery) ?>"
            autocomplete="off"
        >


        <button
            type="submit"
            class="search-button"
        >
            Search
        </button>

    </form>


    <!-- RESULTS HEADING -->

    <?php if ($searchQuery !== "" && !empty($results)): ?>

        <div class="results-top">

            <h2 class="results-heading">

                Results for
                "<?= htmlspecialchars($searchQuery) ?>"

            </h2>


            <div class="results-count">

                <?= count($results) ?> result(s)

            </div>

        </div>

    <?php endif; ?>


    <!-- NO RESULTS -->

    <?php if ($searchQuery !== "" && empty($results)): ?>

        <div class="empty">

            <div class="empty-icon">
                🔎
            </div>


            <h2>
                Nothing found
            </h2>


            <p>

                No movies or TV shows found for
                "<strong>
                    <?= htmlspecialchars($searchQuery) ?>
                </strong>".

                Try searching for another title.

            </p>

        </div>

    <?php endif; ?>


    <!-- RESULTS -->

    <div class="results-grid">

        <?php foreach ($results as $item): ?>

            <?php

            /*
             * Only show movies and TV shows.
             */

            if (
                !isset($item["media_type"]) ||
                !in_array(
                    $item["media_type"],
                    ["movie", "tv"]
                )
            ) {
                continue;
            }


            /*
             * Get title.
             */

            $title =
                $item["title"]
                ?? $item["name"]
                ?? "Untitled";


            /*
             * Get poster.
             */

            $poster =
                $item["poster_path"]
                ?? null;


            /*
             * Get rating.
             */

            $rating =
                $item["vote_average"]
                ?? 0;


            /*
             * Get TMDb ID.
             */

            $mediaId =
                $item["id"]
                ?? 0;


            /*
             * Get media type.
             */

            $mediaType =
                $item["media_type"];

            ?>


            <!-- CLICKABLE CARD -->

            <a
                href="details.php?id=<?= urlencode($mediaId) ?>&type=<?= urlencode($mediaType) ?>"
                class="movie-card"
            >


                <?php if ($poster): ?>

                    <div class="poster-wrapper">

                        <img
                            class="poster"
                            src="<?= TMDB_IMAGE_URL . htmlspecialchars($poster) ?>"
                            alt="<?= htmlspecialchars($title) ?>"
                            loading="lazy"
                        >


                        <div class="poster-overlay"></div>


                        <div class="type-badge">

                            <?= $mediaType === "movie"
                                ? "Movie"
                                : "TV Series"
                            ?>

                        </div>


                        <div class="rating-badge">

                            ⭐
                            <?= number_format(
                                (float)$rating,
                                1
                            ) ?>

                        </div>

                    </div>

                <?php else: ?>

                    <div class="poster-placeholder">

                        <div class="placeholder-icon">
                            🎬
                        </div>

                        No poster available

                    </div>

                <?php endif; ?>


                <div class="movie-info">

                    <h3 class="movie-title">

                        <?= htmlspecialchars(
                            $title
                        ) ?>

                    </h3>


                    <div class="movie-type">

                        <?= $mediaType === "movie"
                            ? "Movie"
                            : "TV Series"
                        ?>

                    </div>


                    <div class="rating">

                        <span>★</span>

                        <?= number_format(
                            (float)$rating,
                            1
                        ) ?>/10

                    </div>

                </div>


            </a>

        <?php endforeach; ?>

    </div>


</main>


</body>

</html>


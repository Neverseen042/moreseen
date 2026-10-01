
<?php

require_once __DIR__ . "/../../config/tmdb.php";
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/dbconnect.php";


/* =========================
   GET MEDIA ID AND TYPE
========================= */

$id = $_GET["id"] ?? "";
$type = $_GET["type"] ?? "";

if (
    empty($id) ||
    !in_array($type, ["movie", "tv"])
) {
    die("Invalid media information.");
}


/* =========================
   GET DETAILS FROM TMDB
========================= */

$url = $tmdbBaseUrl
    . "/" . $type
    . "/" . urlencode($id)
    . "?api_key="
    . urlencode($tmdbApiKey)
    . "&append_to_response=credits";


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);

if ($response === false) {

    curl_close($ch);

    die("Unable to connect to TMDb.");
}

curl_close($ch);


$data = json_decode($response, true);


if (isset($data["status_code"])) {
    die("Media not found.");
}


/* =========================
   MEDIA INFORMATION
========================= */

$title =
    $data["title"]
    ?? $data["name"]
    ?? "Untitled";


$overview =
    $data["overview"]
    ?? "No overview available.";


$poster =
    $data["poster_path"]
    ?? null;


$backdrop =
    $data["backdrop_path"]
    ?? null;


$rating =
    $data["vote_average"]
    ?? 0;


$releaseDate =
    $data["release_date"]
    ?? $data["first_air_date"]
    ?? null;


$genres =
    $data["genres"]
    ?? [];


$mediaLabel =
    $type === "movie"
        ? "Movie"
        : "TV Series";


$backdropUrl =
    $backdrop
        ? "https://image.tmdb.org/t/p/original"
            . $backdrop
        : "";


$posterUrl =
    $poster
        ? TMDB_IMAGE_URL . $poster
        : "https://via.placeholder.com/500x750?text=No+Poster";


$year =
    $releaseDate
        ? date("Y", strtotime($releaseDate))
        : "N/A";


/* =========================
   SAVE MEDIA TO DATABASE
========================= */

$mediaQuery = $pdo->prepare("
    SELECT id
    FROM media
    WHERE tmdb_id = ?
    AND media_type = ?
    LIMIT 1
");

$mediaQuery->execute([
    $id,
    $type
]);

$media = $mediaQuery->fetch();


if ($media) {

    $mediaId = $media["id"];

} else {

    $insertMedia = $pdo->prepare("
        INSERT INTO media (
            tmdb_id,
            media_type,
            title,
            poster_path,
            backdrop_path,
            overview,
            release_date,
            rating
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertMedia->execute([
        $id,
        $type,
        $title,
        $poster,
        $backdrop,
        $overview,
        $releaseDate ?: null,
        $rating
    ]);

    $mediaId = $pdo->lastInsertId();
}


/* =========================
   GET RECOMMENDATION COUNT
========================= */

$countQuery = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM recommendations
    WHERE media_id = ?
");

$countQuery->execute([$mediaId]);

$recommendationCount =
    $countQuery->fetch()["total"] ?? 0;


/* =========================
   CAST
========================= */

$cast =
    $data["credits"]["cast"]
    ?? [];

$cast =
    array_slice($cast, 0, 6);

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
    <?= htmlspecialchars($title) ?> | Moreseen
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

    background: #0b0d10;

    color: white;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    min-height: 100vh;
}


/* =========================
   NAVBAR
========================= */

.navbar {

    height: 75px;

    width: 100%;

    padding: 0 7%;

    display: flex;

    justify-content: space-between;

    align-items: center;

    position: absolute;

    top: 0;

    left: 0;

    z-index: 20;

    background:
        linear-gradient(
            to bottom,
            rgba(5,6,8,0.75),
            transparent
        );
}


.logo {

    color: #d4af37;

    font-size: 27px;

    font-weight: bold;

    letter-spacing: 1px;

    text-decoration: none;
}


.back-link {

    color: #ddd;

    text-decoration: none;

    font-size: 14px;

    padding: 9px 14px;

    border:
        1px solid rgba(255,255,255,0.15);

    border-radius: 8px;

    background:
        rgba(0,0,0,0.25);

    transition: 0.2s;

    backdrop-filter: blur(8px);
}


.back-link:hover {

    color: #d4af37;

    border-color:
        rgba(212,175,55,0.5);
}


/* =========================
   HERO
========================= */

.hero {

    min-height: 720px;

    position: relative;

    display: flex;

    align-items: center;

    overflow: hidden;

    background:
        #0b0d10;
}


.hero-backdrop {

    position: absolute;

    inset: 0;

    background-image:
        url("<?= htmlspecialchars($backdropUrl) ?>");

    background-size: cover;

    background-position: center;

    opacity: 0.58;

    transform: scale(1.02);
}


.hero-overlay {

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            90deg,
            #0b0d10 5%,
            rgba(11,13,16,0.88) 38%,
            rgba(11,13,16,0.48) 72%,
            rgba(11,13,16,0.78) 100%
        );
}


.hero-bottom {

    position: absolute;

    left: 0;

    right: 0;

    bottom: 0;

    height: 300px;

    background:
        linear-gradient(
            to top,
            #0b0d10,
            transparent
        );
}


/* =========================
   HERO CONTENT
========================= */

.hero-content {

    width: 86%;

    max-width: 1250px;

    margin: 0 auto;

    padding-top: 65px;

    position: relative;

    z-index: 5;

    display: flex;

    align-items: center;

    gap: 48px;
}


/* =========================
   POSTER
========================= */

.poster {

    width: 280px;

    flex-shrink: 0;

    border-radius: 16px;

    overflow: hidden;

    border:
        1px solid rgba(255,255,255,0.14);

    box-shadow:
        0 30px 80px rgba(0,0,0,0.65);
}


.poster img {

    width: 100%;

    display: block;

    aspect-ratio: 2 / 3;

    object-fit: cover;
}


/* =========================
   INFORMATION
========================= */

.info {

    max-width: 720px;

    padding-bottom: 15px;
}


.badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    background:
        rgba(212,175,55,0.12);

    border:
        1px solid rgba(212,175,55,0.35);

    color: #d4af37;

    padding: 7px 12px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;

    letter-spacing: 1px;

    margin-bottom: 17px;
}


h1 {

    font-size: 56px;

    line-height: 1.02;

    letter-spacing: -1.5px;

    margin-bottom: 17px;

    text-shadow:
        0 4px 25px rgba(0,0,0,0.4);
}


.meta {

    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 12px;

    color: #bbb;

    margin-bottom: 20px;

    font-size: 14px;
}


.meta-dot {

    color: #555;
}


.rating {

    color: #d4af37;

    font-weight: bold;

    display: inline-flex;

    align-items: center;

    gap: 5px;
}


.overview {

    color: #d0d0d0;

    font-size: 16px;

    line-height: 1.75;

    margin-bottom: 23px;

    max-width: 680px;
}


/* =========================
   GENRES
========================= */

.genres {

    display: flex;

    gap: 8px;

    flex-wrap: wrap;

    margin-bottom: 28px;
}


.genre {

    border:
        1px solid rgba(255,255,255,0.16);

    background:
        rgba(255,255,255,0.05);

    padding: 7px 12px;

    border-radius: 20px;

    font-size: 12px;

    color: #ddd;

    backdrop-filter: blur(6px);
}


/* =========================
   BUTTONS
========================= */

.actions {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;
}


.btn {

    text-decoration: none;

    padding: 13px 20px;

    border-radius: 9px;

    font-size: 14px;

    font-weight: bold;

    transition:
        transform 0.2s,
        background 0.2s,
        border-color 0.2s;
}


.primary {

    background: #d4af37;

    color: #111;

    box-shadow:
        0 8px 25px rgba(212,175,55,0.15);
}


.primary:hover {

    background: #e8c45a;

    transform: translateY(-2px);
}


.secondary {

    background:
        rgba(255,255,255,0.08);

    color: white;

    border:
        1px solid rgba(255,255,255,0.16);

    backdrop-filter: blur(8px);
}


.secondary:hover {

    border-color:
        rgba(212,175,55,0.45);

    color: #d4af37;

    transform: translateY(-2px);
}


/* =========================
   CONTENT SECTIONS
========================= */

.section {

    width: 86%;

    max-width: 1250px;

    margin: 0 auto;

    padding: 55px 0;
}


.section-heading {

    display: flex;

    align-items: center;

    gap: 15px;

    margin-bottom: 23px;
}


.section-heading h2 {

    font-size: 27px;
}


.section-line {

    height: 1px;

    flex: 1;

    background:
        rgba(255,255,255,0.08);
}


/* =========================
   COMMUNITY
========================= */

.community {

    background:
        linear-gradient(
            135deg,
            #171a1f,
            #111318
        );

    border:
        1px solid #282c34;

    border-radius: 17px;

    padding: 28px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 25px;

    position: relative;

    overflow: hidden;
}


.community::before {

    content: "";

    position: absolute;

    left: 0;

    top: 0;

    bottom: 0;

    width: 4px;

    background: #d4af37;
}


.community-content {

    padding-left: 5px;
}


.community h3 {

    font-size: 18px;

    margin-bottom: 8px;
}


.community p {

    color: #999;

    line-height: 1.6;

    font-size: 14px;
}


.tally {

    font-size: 36px;

    color: #d4af37;

    font-weight: bold;

    text-align: right;

    white-space: nowrap;
}


.tally-label {

    font-size: 11px;

    color: #777;

    font-weight: normal;

    text-transform: uppercase;

    letter-spacing: 1px;

    margin-top: 3px;
}


/* =========================
   CAST
========================= */

.cast-grid {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(150px, 1fr)
        );

    gap: 18px;
}


.cast-card {

    background:
        linear-gradient(
            145deg,
            #171a1f,
            #111318
        );

    border:
        1px solid #272b32;

    border-radius: 13px;

    overflow: hidden;

    transition:
        transform 0.25s ease,
        border-color 0.25s ease;
}


.cast-card:hover {

    transform: translateY(-5px);

    border-color:
        rgba(212,175,55,0.35);
}


.cast-card img {

    width: 100%;

    height: 225px;

    object-fit: cover;

    display: block;
}


.cast-info {

    padding: 13px;
}


.cast-name {

    font-weight: bold;

    font-size: 14px;

    margin-bottom: 6px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


.character {

    color: #888;

    font-size: 12px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* =========================
   MOBILE
========================= */

@media (max-width: 800px) {

    .navbar {

        padding: 0 5%;

        height: 68px;
    }


    .logo {

        font-size: 23px;
    }


    .back-link {

        font-size: 12px;

        padding: 8px 10px;
    }


    .hero {

        min-height: auto;

        padding-bottom: 55px;
    }


    .hero-backdrop {

        opacity: 0.35;

        background-position:
            center top;
    }


    .hero-overlay {

        background:
            linear-gradient(
                to bottom,
                rgba(11,13,16,0.55),
                #0b0d10 80%
            );
    }


    .hero-bottom {

        height: 220px;
    }


    .hero-content {

        width: 92%;

        padding-top: 120px;

        flex-direction: column;

        align-items: flex-start;

        gap: 28px;
    }


    .poster {

        width: 190px;

        border-radius: 13px;
    }


    .info {

        width: 100%;
    }


    h1 {

        font-size: 38px;

        letter-spacing: -0.7px;
    }


    .overview {

        font-size: 14px;

        line-height: 1.7;
    }


    .section {

        width: 92%;

        padding: 40px 0;
    }


    .section-heading h2 {

        font-size: 22px;
    }


    .community {

        flex-direction: column;

        align-items: flex-start;

        padding: 23px;
    }


    .tally {

        text-align: left;

        font-size: 30px;
    }


    .cast-grid {

        grid-template-columns:
            repeat(2, 1fr);

        gap: 12px;
    }


    .cast-card img {

        height: 210px;
    }

}


/* =========================
   SMALL PHONES
========================= */

@media (max-width: 400px) {

    .hero-content {

        padding-top: 105px;
    }


    .poster {

        width: 160px;
    }


    h1 {

        font-size: 32px;
    }


    .btn {

        width: 100%;

        text-align: center;
    }


    .cast-card img {

        height: 180px;
    }

}

</style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">

    <a
        href="search.php"
        class="logo"
    >
        Moreseen
    </a>


    <a
        href="search.php"
        class="back-link"
    >
        ← Back to Search
    </a>

</nav>


<!-- =========================
     HERO
========================= -->

<section class="hero">


    <?php if ($backdropUrl): ?>

        <div class="hero-backdrop"></div>

    <?php endif; ?>


    <div class="hero-overlay"></div>

    <div class="hero-bottom"></div>


    <div class="hero-content">


        <!-- POSTER -->

        <div class="poster">

            <img
                src="<?= htmlspecialchars($posterUrl) ?>"
                alt="<?= htmlspecialchars($title) ?>"
            >

        </div>


        <!-- INFORMATION -->

        <div class="info">

            <span class="badge">

                <?= $mediaLabel ?>

            </span>


            <h1>

                <?= htmlspecialchars($title) ?>

            </h1>


            <div class="meta">

                <span>
                    <?= htmlspecialchars($year) ?>
                </span>


                <span class="meta-dot">
                    •
                </span>


                <span class="rating">

                    ★

                    <?= number_format(
                        (float)$rating,
                        1
                    ) ?>

                    / 10

                </span>

            </div>


            <p class="overview">

                <?= htmlspecialchars($overview) ?>

            </p>


            <!-- GENRES -->

            <?php if (!empty($genres)): ?>

                <div class="genres">

                    <?php foreach ($genres as $genre): ?>

                        <span class="genre">

                            <?= htmlspecialchars(
                                $genre["name"]
                            ) ?>

                        </span>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- ACTIONS -->

            <div class="actions">

                <a
                    href="../recommendations/create.php?id=<?= urlencode($id) ?>&type=<?= urlencode($type) ?>"
                    class="btn primary"
                >
                    ✍ Recommend This
                </a>


                <a
                    href="#cast"
                    class="btn secondary"
                >
                    👥 View Cast
                </a>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     COMMUNITY
========================= -->

<section
    class="section"
    id="community"
>

    <div class="section-heading">

        <h2>
            Moreseen Community
        </h2>

        <div class="section-line"></div>

    </div>


    <div class="community">

        <div class="community-content">

            <h3>
                Community Recommendations
            </h3>


            <p>

                See what people on Moreseen think
                about
                <?= htmlspecialchars($title) ?>.

            </p>

        </div>


        <div class="tally">

            <?= number_format(
                (int)$recommendationCount
            ) ?>


            <div class="tally-label">

                Recommendations

            </div>

        </div>

    </div>

</section>


<!-- =========================
     CAST
========================= -->

<?php if (!empty($cast)): ?>

<section
    class="section"
    id="cast"
>

    <div class="section-heading">

        <h2>
            Top Cast
        </h2>

        <div class="section-line"></div>

    </div>


    <div class="cast-grid">

        <?php foreach ($cast as $person): ?>

            <?php

            $castImage =
                !empty($person["profile_path"])

                ? "https://image.tmdb.org/t/p/w300"
                    . $person["profile_path"]

                : "https://via.placeholder.com/300x450?text=No+Image";

            ?>


            <div class="cast-card">

                <img
                    src="<?= htmlspecialchars($castImage) ?>"
                    alt="<?= htmlspecialchars(
                        $person["name"] ?? ""
                    ) ?>"
                    loading="lazy"
                >


                <div class="cast-info">

                    <div class="cast-name">

                        <?= htmlspecialchars(
                            $person["name"]
                            ?? "Unknown"
                        ) ?>

                    </div>


                    <div class="character">

                        <?= htmlspecialchars(
                            $person["character"]
                            ?? ""
                        ) ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>

<?php endif; ?>


</body>

</html>


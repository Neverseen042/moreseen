
<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/dbconnect.php";
require_once __DIR__ . "/../../helpers/auth.php";

requireLogin();


$userId = currentUserId();


/*
|--------------------------------------------------------------------------
| GET USER INFORMATION
|--------------------------------------------------------------------------
*/

$userQuery = $pdo->prepare("
    SELECT
        id,
        name,
        email,
        avatar,
        bio,
        created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");

$userQuery->execute([$userId]);

$user = $userQuery->fetch();


if (!$user) {

    session_destroy();

    header("Location: ../auth/login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET RECOMMENDATION COUNT
|--------------------------------------------------------------------------
*/

$recommendationQuery = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM recommendations
    WHERE user_id = ?
");

$recommendationQuery->execute([$userId]);

$recommendationCount =
    $recommendationQuery->fetch()["total"] ?? 0;


/*
|--------------------------------------------------------------------------
| GET TOTAL TIPS RECEIVED
|--------------------------------------------------------------------------
*/

$tipQuery = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) AS total
    FROM tips
    WHERE receiver_id = ?
    AND status = 'success'
");

$tipQuery->execute([$userId]);

$totalTips =
    $tipQuery->fetch()["total"] ?? 0;


/*
|--------------------------------------------------------------------------
| MEMBER SINCE
|--------------------------------------------------------------------------
*/

$memberSince =
    !empty($user["created_at"])
        ? date("F Y", strtotime($user["created_at"]))
        : "Recently";

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
        <?= htmlspecialchars($user["name"]) ?> | Moreseen
    </title>


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
                    rgba(212,175,55,0.09),
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

            width: 86%;

            max-width: 1200px;

            margin: auto;

            padding: 60px 0 90px;
        }


        .welcome {

            margin-bottom: 35px;
        }


        .welcome-label {

            color: #d4af37;

            text-transform: uppercase;

            font-size: 11px;

            font-weight: bold;

            letter-spacing: 2.5px;

            margin-bottom: 12px;
        }


        .welcome h1 {

            font-size: 44px;

            line-height: 1.1;

            letter-spacing: -1px;

            margin-bottom: 12px;
        }


        .welcome p {

            color: #929292;

            font-size: 15px;

            line-height: 1.7;

            max-width: 650px;
        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE CARD
        |--------------------------------------------------------------------------
        */

        .profile-card {

            background:
                linear-gradient(
                    135deg,
                    rgba(25,28,34,0.98),
                    rgba(16,18,23,0.98)
                );

            border:
                1px solid #282c34;

            border-radius: 18px;

            padding: 30px;

            display: flex;

            align-items: center;

            gap: 25px;

            margin-bottom: 30px;

            box-shadow:
                0 20px 50px rgba(0,0,0,0.25);

            position: relative;

            overflow: hidden;
        }


        .profile-card::before {

            content: "";

            position: absolute;

            top: 0;

            left: 0;

            width: 4px;

            height: 100%;

            background: #d4af37;
        }


        .avatar {

            width: 90px;

            height: 90px;

            border-radius: 50%;

            background:
                linear-gradient(
                    135deg,
                    #d4af37,
                    #8f7220
                );

            display: flex;

            align-items: center;

            justify-content: center;

            color: #111;

            font-size: 34px;

            font-weight: bold;

            flex-shrink: 0;

            overflow: hidden;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.25);

            border:
                3px solid rgba(212,175,55,0.15);
        }


        .avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;
        }


        .profile-info h2 {

            font-size: 25px;

            margin-bottom: 7px;
        }


        .email {

            color: #999;

            font-size: 14px;

            margin-bottom: 8px;
        }


        .member {

            color: #777;

            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-bottom: 40px;
        }


        .stat {

            background:
                linear-gradient(
                    135deg,
                    #171a1f,
                    #111318
                );

            border:
                1px solid #282c34;

            border-radius: 15px;

            padding: 25px;

            transition:
                transform 0.25s ease,
                border-color 0.25s ease,
                box-shadow 0.25s ease;

            position: relative;

            overflow: hidden;
        }


        .stat::after {

            content: "";

            position: absolute;

            width: 70px;

            height: 70px;

            right: -25px;

            bottom: -25px;

            border-radius: 50%;

            background:
                rgba(212,175,55,0.05);
        }


        .stat:hover {

            transform: translateY(-4px);

            border-color:
                rgba(212,175,55,0.4);

            box-shadow:
                0 15px 35px rgba(0,0,0,0.2);
        }


        .stat-number {

            color: #d4af37;

            font-size: 30px;

            font-weight: bold;

            margin-bottom: 8px;

            position: relative;

            z-index: 1;
        }


        .stat-label {

            color: #999;

            font-size: 13px;

            position: relative;

            z-index: 1;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */

        .section-heading {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 18px;
        }


        .section-title {

            font-size: 23px;
        }


        .section-line {

            flex: 1;

            height: 1px;

            background:
                rgba(255,255,255,0.07);

            margin-left: 20px;
        }


        .actions {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 18px;
        }


        .action-card {

            text-decoration: none;

            color: white;

            background:
                linear-gradient(
                    135deg,
                    #171a1f,
                    #111318
                );

            border:
                1px solid #282c34;

            border-radius: 15px;

            padding: 25px;

            transition:
                transform 0.25s ease,
                border-color 0.25s ease,
                box-shadow 0.25s ease;

            position: relative;

            overflow: hidden;
        }


        .action-card::after {

            content: "→";

            position: absolute;

            right: 22px;

            top: 22px;

            color: #555;

            font-size: 20px;

            transition: 0.2s;
        }


        .action-card:hover {

            transform: translateY(-5px);

            border-color:
                rgba(212,175,55,0.45);

            box-shadow:
                0 15px 35px rgba(0,0,0,0.25);
        }


        .action-card:hover::after {

            color: #d4af37;

            transform: translateX(4px);
        }


        .action-icon {

            width: 48px;

            height: 48px;

            border-radius: 12px;

            background:
                rgba(212,175,55,0.08);

            border:
                1px solid rgba(212,175,55,0.15);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 23px;

            margin-bottom: 17px;
        }


        .action-card h3 {

            font-size: 17px;

            margin-bottom: 8px;

            padding-right: 30px;
        }


        .action-card p {

            color: #888;

            font-size: 14px;

            line-height: 1.6;

            max-width: 480px;
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

                padding: 40px 0 70px;
            }


            .welcome {

                margin-bottom: 28px;
            }


            .welcome h1 {

                font-size: 32px;

                letter-spacing: -0.5px;
            }


            .welcome p {

                font-size: 14px;
            }


            .profile-card {

                align-items: flex-start;

                flex-direction: column;

                padding: 25px;

                gap: 18px;
            }


            .avatar {

                width: 75px;

                height: 75px;

                font-size: 28px;
            }


            .profile-info h2 {

                font-size: 22px;
            }


            .stats {

                grid-template-columns: 1fr;

                gap: 12px;

                margin-bottom: 32px;
            }


            .stat {

                padding: 20px;
            }


            .stat-number {

                font-size: 27px;
            }


            .section-heading {

                margin-bottom: 15px;
            }


            .section-title {

                font-size: 21px;
            }


            .section-line {

                margin-left: 12px;
            }


            .actions {

                grid-template-columns: 1fr;

                gap: 12px;
            }


            .action-card {

                padding: 21px;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | SMALL PHONES
        |--------------------------------------------------------------------------
        */

        @media (max-width: 400px) {

            .welcome h1 {

                font-size: 28px;
            }


            .profile-card {

                padding: 20px;
            }


            .action-card {

                padding: 19px;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar">

    <a
        href="../media/search.php"
        class="logo"
    >
        Moreseen
    </a>


    <div class="nav-right">

        <a
            href="../media/search.php"
            class="nav-link"
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
            href="../../controllers/AuthController.php?action=logout"
            class="nav-link logout"
        >
            Logout
        </a>

    </div>

</nav>


<!-- MAIN -->

<main class="container">


    <!-- WELCOME -->

    <div class="welcome">

        <div class="welcome-label">

            Your Moreseen

        </div>


        <h1>

            Welcome back,
            <?= htmlspecialchars($user["name"]) ?>.

        </h1>


        <p>

            Discover something great and share your next
            recommendation with the community.

        </p>

    </div>


    <!-- PROFILE -->

    <div class="profile-card">


        <div class="avatar">

            <?php if (!empty($user["avatar"])): ?>

                <img
                    src="<?= htmlspecialchars($user["avatar"]) ?>"
                    alt="Profile picture"
                >

            <?php else: ?>

                <?= htmlspecialchars(
                    strtoupper(
                        substr(
                            $user["name"],
                            0,
                            1
                        )
                    )
                ) ?>

            <?php endif; ?>

        </div>


        <div class="profile-info">

            <h2>

                <?= htmlspecialchars(
                    $user["name"]
                ) ?>

            </h2>


            <div class="email">

                <?= htmlspecialchars(
                    $user["email"]
                ) ?>

            </div>


            <div class="member">

                Member since
                <?= htmlspecialchars(
                    $memberSince
                ) ?>

            </div>

        </div>

    </div>


    <!-- STATS -->

    <div class="stats">


        <div class="stat">

            <div class="stat-number">

                <?= number_format(
                    (int)$recommendationCount
                ) ?>

            </div>


            <div class="stat-label">

                Recommendations

            </div>

        </div>


        <div class="stat">

            <div class="stat-number">

                ₦<?= number_format(
                    (int)$totalTips
                ) ?>

            </div>


            <div class="stat-label">

                Tips Received

            </div>

        </div>


        <div class="stat">

            <div class="stat-number">

                <?= htmlspecialchars(
                    $memberSince
                ) ?>

            </div>


            <div class="stat-label">

                Member Since

            </div>

        </div>


    </div>


    <!-- ACTIONS -->

    <div class="section-heading">

        <h2 class="section-title">

            What do you want to do?

        </h2>


        <div class="section-line"></div>

    </div>


    <div class="actions">


        <a
            href="../media/search.php"
            class="action-card"
        >

            <div class="action-icon">
                🎬
            </div>


            <h3>

                Discover Movies & TV

            </h3>


            <p>

                Search TMDb and find something
                worth watching.

            </p>

        </a>


        <a
            href="../recommendations/create.php"
            class="action-card"
        >

            <div class="action-icon">
                ✍️
            </div>


            <h3>

                Write a Recommendation

            </h3>


            <p>

                Tell the Moreseen community why
                they should watch something.

            </p>

        </a>


        <a
            href="../recommendations/feed.php"
            class="action-card"
        >

            <div class="action-icon">
                🔥
            </div>


            <h3>

                Community Recommendations

            </h3>


            <p>

                See what other people are recommending
                and discover new titles.

            </p>

        </a>


        <a
            href="recommendations.php"
            class="action-card"
        >

            <div class="action-icon">
                📚
            </div>


            <h3>

                My Recommendations

            </h3>


            <p>

                View and manage the recommendations
                you have written.

            </p>

        </a>


    </div>


</main>


</body>

</html>


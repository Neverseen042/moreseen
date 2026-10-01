<?php

require_once __DIR__ . "/../config/dbconnect.php";


class Recommendation
{

    public static function create(
        $userId,
        $mediaId,
        $content
    ) {
        global $pdo;

        $query = $pdo->prepare("
            INSERT INTO recommendations (
                user_id,
                media_id,
                content
            )
            VALUES (?, ?, ?)
        ");

        $query->execute([
            $userId,
            $mediaId,
            $content
        ]);

        return $pdo->lastInsertId();
    }


    public static function exists(
        $userId,
        $mediaId
    ) {
        global $pdo;

        $query = $pdo->prepare("
            SELECT id
            FROM recommendations
            WHERE user_id = ?
            AND media_id = ?
            LIMIT 1
        ");

        $query->execute([
            $userId,
            $mediaId
        ]);

        return $query->fetch() !== false;
    }


    public static function countForMedia($mediaId)
    {
        global $pdo;

        $query = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM recommendations
            WHERE media_id = ?
        ");

        $query->execute([$mediaId]);

        $result = $query->fetch();

        return (int)$result["total"];
    }


    public static function getByUser($userId)
    {
        global $pdo;

        $query = $pdo->prepare("
            SELECT
                recommendations.*,
                media.title,
                media.poster_path,
                media.media_type,
                media.tmdb_id
            FROM recommendations

            INNER JOIN media
                ON recommendations.media_id = media.id

            WHERE recommendations.user_id = ?

            ORDER BY recommendations.created_at DESC
        ");

        $query->execute([$userId]);

        return $query->fetchAll();
    }


    public static function getFeed()
    {
        global $pdo;

        $query = $pdo->prepare("
            SELECT
                recommendations.*,

                users.name AS user_name,
                users.avatar AS user_avatar,

                media.title,
                media.poster_path,
                media.media_type,
                media.tmdb_id

            FROM recommendations

            INNER JOIN users
                ON recommendations.user_id = users.id

            INNER JOIN media
                ON recommendations.media_id = media.id

            ORDER BY recommendations.created_at DESC
        ");

        $query->execute();

        return $query->fetchAll();
    }


    public static function findById($id)
    {
        global $pdo;

        $query = $pdo->prepare("
            SELECT

                recommendations.*,

                users.name AS user_name,
                users.avatar AS user_avatar,

                media.title,
                media.poster_path,
                media.backdrop_path,
                media.overview,
                media.media_type,
                media.tmdb_id,
                media.rating

            FROM recommendations

            INNER JOIN users
                ON recommendations.user_id = users.id

            INNER JOIN media
                ON recommendations.media_id = media.id

            WHERE recommendations.id = ?

            LIMIT 1
        ");

        $query->execute([$id]);

        return $query->fetch();
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE RECOMMENDATION
    |--------------------------------------------------------------------------
    */

    public static function update(
        $id,
        $userId,
        $content
    ) {
        global $pdo;

        $query = $pdo->prepare("
            UPDATE recommendations

            SET content = ?

            WHERE id = ?

            AND user_id = ?
        ");

        $query->execute([
            $content,
            $id,
            $userId
        ]);

        return $query->rowCount() > 0;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE RECOMMENDATION
    |--------------------------------------------------------------------------
    */

    public static function delete(
        $id,
        $userId
    ) {
        global $pdo;

        $query = $pdo->prepare("
            DELETE FROM recommendations

            WHERE id = ?

            AND user_id = ?
        ");

        $query->execute([
            $id,
            $userId
        ]);

        return $query->rowCount() > 0;
    }

}


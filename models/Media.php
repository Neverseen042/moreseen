<?php

require_once __DIR__ . "/../config/dbconnect.php";


class Media
{

    /*
    |--------------------------------------------------------------------------
    | FIND MEDIA
    |--------------------------------------------------------------------------
    */

    public static function findByTmdbId($tmdbId, $mediaType)
    {

        global $pdo;

        $query = $pdo->prepare("
            SELECT *
            FROM media
            WHERE tmdb_id = ?
            AND media_type = ?
            LIMIT 1
        ");

        $query->execute([
            $tmdbId,
            $mediaType
        ]);

        return $query->fetch();
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE MEDIA
    |--------------------------------------------------------------------------
    */

    public static function create($data)
    {

        global $pdo;

        $query = $pdo->prepare("
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

        $query->execute([

            $data["tmdb_id"],
            $data["media_type"],
            $data["title"],
            $data["poster_path"] ?? null,
            $data["backdrop_path"] ?? null,
            $data["overview"] ?? null,
            $data["release_date"] ?? null,
            $data["rating"] ?? null

        ]);

        return $pdo->lastInsertId();
    }


    /*
    |--------------------------------------------------------------------------
    | FIND OR CREATE MEDIA
    |--------------------------------------------------------------------------
    */

    public static function findOrCreate($data)
    {

        $existing = self::findByTmdbId(
            $data["tmdb_id"],
            $data["media_type"]
        );


        if ($existing) {

            return $existing["id"];

        }


        return self::create($data);
    }


    /*
    |--------------------------------------------------------------------------
    | GET MEDIA BY ID
    |--------------------------------------------------------------------------
    */

    public static function findById($id)
    {

        global $pdo;

        $query = $pdo->prepare("
            SELECT *
            FROM media
            WHERE id = ?
            LIMIT 1
        ");

        $query->execute([$id]);

        return $query->fetch();
    }

}
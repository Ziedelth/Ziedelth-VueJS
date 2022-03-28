<?php

class AnimeMapper
{
    /**
     * Get all the animes from the database, ordered by name, from the country with the tag $country
     *
     * @param PDO $pdo The PDO object that we created earlier.
     * @param string $country The country tag of the country you want to get all the animes from.
     *
     * @return array|false An array of associative arrays.
     */
    static function getAllAnimes(PDO $pdo, string $country)
    {
        $request = $pdo->prepare("SELECT animes.id AS id, animes.release_date, animes.name AS name, animes.description AS description, animes.image AS image
FROM jais.animes
         INNER JOIN jais.countries c on animes.country_id = c.id
WHERE c.tag = :country
ORDER BY animes.name");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all the seasons and episodes for a given anime
     *
     * @param PDO $pdo The PDO object that we created earlier.
     * @param int $animeId The ID of the anime you want to get the seasons for.
     *
     * @return array|false An array of associative arrays. Each associative array has two keys:
     *     - season: The season number
     *     - episodes: A comma-separated list of episode IDs
     */
    static function getSeasons(PDO $pdo, int $animeId)
    {
        $request = $pdo->prepare("SELECT e.season AS season, GROUP_CONCAT(e.id) AS episodes
FROM jais.episodes e
WHERE e.anime_id = :anime_id
GROUP BY season");
        $request->execute(array('anime_id' => $animeId));
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all the scans for a given anime
     *
     * @param PDO $pdo The PDO object that we created earlier.
     * @param int $animeId The ID of the anime you want to get the scans for.
     *
     * @return array|false An array of the scans for the anime.
     */
    static function getScans(PDO $pdo, int $animeId)
    {
        $request = $pdo->prepare("SELECT GROUP_CONCAT(s.id) AS scans
FROM jais.scans s
WHERE s.anime_id = :anime_id
GROUP BY s.anime_id");
        $request->execute(array('anime_id' => $animeId));
        return $request->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get an anime by id
     *
     * @param PDO $pdo The PDO object that we created earlier.
     * @param string $country The country code of the country you want to get the anime from.
     * @param int $id The id of the anime you want to get.
     *
     * @return array|false An array of the anime, its seasons, and its scans.
     */
    static function getById(PDO $pdo, string $country, int $id)
    {
        $request = $pdo->prepare("SELECT a.*, c.season AS country_season, GROUP_CONCAT(g.$country SEPARATOR ', ') AS genres
FROM jais.animes a
         LEFT JOIN jais.countries c on a.country_id = c.id
         LEFT JOIN jais.anime_genres ag ON ag.anime_id = a.id
         LEFT JOIN jais.genres g ON g.id = ag.genre_id
WHERE a.id = :id
GROUP BY a.id");
        $request->execute(array('id' => $id));
        $anime = $request->fetch(PDO::FETCH_ASSOC);
        $seasons = self::getSeasons($pdo, $id);

        if ($seasons) {
            foreach ($seasons as $season) {
                $anime['seasons'][] = array('season' => $season['season'], 'episodes' => EpisodeMapper::getEpisodesWithIds($pdo, $country, $season['episodes']));
            }
        } else {
            $anime['seasons'] = [];
        }

        $scans = self::getScans($pdo, $id);
        $anime['scans'] = $scans ? ScanMapper::getScansWithIds($pdo, $country, $scans['scans']) : [];

        return $anime;
    }
}
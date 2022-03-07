<?php

class AnimeMapper
{
    /**
     * @param PDO $pdo
     * @param string $country
     * @return array|false
     */
    static function getAllAnimes(PDO $pdo, string $country)
    {
        $request = $pdo->prepare("SELECT animes.id AS id, animes.name AS name, animes.description AS description, animes.image AS image FROM jais.animes INNER JOIN jais.countries c on animes.country_id = c.id WHERE c.tag = :country ORDER BY animes.name");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param PDO $pdo
     * @param int $animeId
     * @return array|false
     */
    static function getSeasons(PDO $pdo, int $animeId)
    {
        $request = $pdo->prepare("SELECT e.season AS season, GROUP_CONCAT(e.id) AS episodes FROM jais.episodes e WHERE e.anime_id = :anime_id GROUP BY season");
        $request->execute(array('anime_id' => $animeId));
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param PDO $pdo
     * @param int $animeId
     * @return array|false
     */
    static function getScans(PDO $pdo, int $animeId)
    {
        $request = $pdo->prepare("SELECT GROUP_CONCAT(s.id) AS scans FROM jais.scans s WHERE s.anime_id = :anime_id GROUP BY s.anime_id");
        $request->execute(array('anime_id' => $animeId));
        return $request->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param PDO $pdo
     * @param string $country
     * @param int $id
     * @return array|false
     */
    static function getById(PDO $pdo, string $country, int $id)
    {
        $request = $pdo->prepare("SELECT a.*, c.season AS country_season, GROUP_CONCAT(g.$country) AS genres FROM jais.animes a INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.anime_genres ag ON ag.anime_id = a.id INNER JOIN jais.genres g ON g.id = ag.genre_id WHERE a.id = :id GROUP BY a.id");
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
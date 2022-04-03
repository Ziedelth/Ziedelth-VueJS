<?php

class EpisodeMapper
{
    // Check if id exists
    public static function exists(PDO $pdo, $id): bool
    {
        $query = $pdo->prepare('SELECT COUNT(*) FROM jais.episodes WHERE id = :id');
        $query->execute(array('id' => $id));
        $rows = $query->rowCount();
        return $rows > 0;
    }

    /**
     * It returns the last episode id for each anime of the given country
     *
     * @param PDO $pdo The PDO object that we created earlier.
     * @param string $country The country tag of the anime you want to get the last ids from.
     *
     * @return array|false An array of the last episode id for each anime.
     */
    static function getLastIds(PDO $pdo, string $country) {
        $request = $pdo->prepare("SELECT episodes.id
FROM jais.episodes
         INNER JOIN jais.animes a on episodes.anime_id = a.id
         INNER JOIN jais.countries c on a.country_id = c.id
WHERE c.tag = :country
ORDER BY episodes.release_date DESC, episodes.anime_id DESC, episodes.season DESC, episodes.number DESC,
         episodes.id_episode_type DESC, episodes.id_lang_type DESC");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get all the episodes with the given ids
     *
     * @param PDO $pdo The PDO object that will be used to execute the query.
     * @param string $country The country code of the country you want to get the episodes for.
     * @param string|null $ids The ids of the episodes to get.
     *
     * @return array|false An array of arrays. Each array contains the following:
     *     - platform
     *     - platform_url
     *     - platform_image
     *     - anime_id
     *     - anime
     *     - id
     *     - release_date
     *     - season
     *     - number
     *     - country_season
     *     - episode_type
     *     - episode_type
     *     - lang_type
     */
    static function getEpisodesWithIds(PDO $pdo, string $country, ?string $ids)
    {
        if ($ids == null && empty($ids))
            return [];

        $request = $pdo->prepare("SELECT p.name                AS platform,
       p.url                 AS platform_url,
       p.image               AS platform_image,
       a.id                  AS anime_id,
       a.name                AS anime,
       episodes.id           AS id,
       episodes.release_date AS release_date,
       episodes.season       AS season,
       episodes.number       AS number,
       c.season              AS country_season,
       et.$country           AS episode_type,
       et.$country           AS episode_type,
       lt.$country           AS lang_type,
       episodes.episode_id   AS episode_id,
       episodes.title        AS title,
       episodes.url          AS url,
       episodes.image        AS image,
       episodes.duration     AS duration,
       (SELECT COALESCE(CAST(SUM(en.count) AS INT), 0) FROM ziedelth.episodes_notations en WHERE en.episode_id = jais.episodes.id AND en.count = 1) AS notation
FROM jais.episodes
         INNER JOIN jais.platforms p on episodes.platform_id = p.id
         INNER JOIN jais.animes a on episodes.anime_id = a.id
         INNER JOIN jais.countries c on a.country_id = c.id
         INNER JOIN jais.episode_types et on episodes.id_episode_type = et.id
         INNER JOIN jais.lang_types lt on lt.id = episodes.id_lang_type
WHERE episodes.id IN ($ids)
ORDER BY episodes.release_date DESC, anime_id DESC, season DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }

    static function isIDExists(PDO $pdo, ?int $id): bool
    {
        if ($id == null && empty($id))
            return false;

        $request = $pdo->prepare("SELECT id
FROM jais.episodes
WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->rowCount() >= 1;
    }
}
<?php

class EpisodeMapper
{
    /**
     * @param PDO $pdo
     * @param string $country
     * @return array|false
     */
    static function getLastIds(PDO $pdo, string $country) {
        $request = $pdo->prepare("SELECT episodes.id FROM jais.episodes INNER JOIN jais.animes a on episodes.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id WHERE c.tag = :country ORDER BY episodes.release_date DESC, episodes.anime_id DESC, episodes.season DESC, episodes.number DESC, episodes.id_episode_type DESC, episodes.id_lang_type DESC");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param PDO $pdo
     * @param string $country
     * @param string|null $ids
     * @return array|false
     */
    static function getEpisodesWithIds(PDO $pdo, string $country, ?string $ids)
    {
        if ($ids == null && empty($ids))
            return [];

        $request = $pdo->prepare("SELECT p.name AS platform, p.url AS platform_url, p.image AS platform_image, a.id AS anime_id, a.name AS anime, episodes.id AS id, episodes.release_date AS release_date, episodes.season AS season, episodes.number AS number, c.season AS country_season, et.$country AS episode_type, et.$country AS episode_type, lt.$country AS lang_type, episodes.episode_id AS episode_id, episodes.title AS title, episodes.url AS url, episodes.image AS image, episodes.duration AS duration FROM jais.episodes INNER JOIN jais.platforms p on episodes.platform_id = p.id INNER JOIN jais.animes a on episodes.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.episode_types et on episodes.id_episode_type = et.id INNER JOIN jais.lang_types lt on lt.id = episodes.id_lang_type WHERE episodes.id IN ($ids) ORDER BY episodes.release_date DESC, anime_id DESC, season DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
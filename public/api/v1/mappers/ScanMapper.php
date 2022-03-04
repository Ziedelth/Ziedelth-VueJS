<?php

class ScanMapper
{
    /**
     * @param PDO $pdo
     * @param string $country
     * @return array|false
     */
    static function getLastIds(PDO $pdo, string $country) {
        $request = $pdo->prepare("SELECT scans.id FROM scans INNER JOIN animes a on scans.anime_id = a.id INNER JOIN countries c on a.country_id = c.id WHERE c.tag = :country ORDER BY scans.release_date DESC, scans.anime_id DESC, scans.number DESC, scans.id_episode_type DESC, scans.id_lang_type DESC");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param PDO $pdo
     * @param string $country
     * @param string $ids
     * @return array|false
     */
    static function getScansWithIds(PDO $pdo, string $country, string $ids)
    {
        if (empty($ids))
            return [];

        $request = $pdo->prepare("SELECT p.name AS platform, p.url AS platform_url, p.image AS platform_image, a.id AS anime_id, a.name AS anime, scans.release_date AS release_date, scans.number AS number, et.$country AS episode_type, lt.$country AS lang_type, scans.url AS url FROM scans INNER JOIN platforms p on scans.platform_id = p.id INNER JOIN animes a on scans.anime_id = a.id INNER JOIN episode_types et on scans.id_episode_type = et.id INNER JOIN lang_types lt on lt.id = scans.id_lang_type WHERE scans.id IN ($ids) ORDER BY scans.release_date DESC, anime_id DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
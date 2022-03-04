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
        $request = $pdo->prepare("SELECT animes.id AS anime_id, animes.name AS anime, animes.description AS anime_description, animes.image AS anime_image FROM animes INNER JOIN countries c on animes.country_id = c.id WHERE c.tag = :country ORDER BY animes.name");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
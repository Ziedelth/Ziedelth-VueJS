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
        $request = $pdo->prepare("SELECT animes.id AS id, animes.name AS name, animes.description AS description, animes.image AS image FROM animes INNER JOIN countries c on animes.country_id = c.id WHERE c.tag = :country ORDER BY animes.name");
        $request->execute(array('country' => $country));
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
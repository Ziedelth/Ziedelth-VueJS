<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class AnimeGenresMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.anime_genres');
    }

    function getGenresByAnimeId(?PDO $pdo, $id): array
    {
        $request = $pdo->prepare("SELECT genre_id FROM $this->tableName WHERE anime_id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchAll(PDO::FETCH_COLUMN) ?? array();
    }
}
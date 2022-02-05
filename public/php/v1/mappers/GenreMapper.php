<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class Genre extends JObject
{
    public int $id;
    public string $name;
    public string $fr;
}

class GenreMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.genres', 'Genre');
    }

    function getGenresByIds(?PDO $pdo, string $ids): array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id IN ($ids)");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className);
    }
}
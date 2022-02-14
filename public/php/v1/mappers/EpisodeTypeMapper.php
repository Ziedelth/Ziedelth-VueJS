<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class EpisodeType extends JObject
{
    public int $id;
    public string $name;
    public string $fr;
}

class EpisodeTypeMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.episode_types', 'EpisodeType');
    }

    function getEpisodeTypeById(?PDO $pdo, int $id): ?EpisodeType
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchObject($this->className);
    }
}
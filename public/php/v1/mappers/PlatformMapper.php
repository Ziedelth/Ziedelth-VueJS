<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class Platform extends JObject
{
    public int $id;
    public string $name;
    public string $url;
    public string $image;
    public int $color;
}

class PlatformMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.platforms', 'Platform');
    }

    function getAllPlatforms(?PDO $pdo): array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className);
    }

    function getPlatformsByIds(?PDO $pdo, string $ids): array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id IN ($ids)");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className);
    }

    function getPlatformById(?PDO $pdo, int $id): ?Platform
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchObject($this->className);
    }

    function getPlatformByName(?PDO $pdo, string $name): ?Platform
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE name = :name");
        $request->execute(array('name' => $name));
        return $request->fetchObject($this->className);
    }
}
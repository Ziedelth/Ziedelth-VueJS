<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class Country extends JObject
{
    public int $id;
    public string $tag;
    public string $name;
    public string $flag;
    public string $season;
}

class CountryMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.countries', 'Country');
    }

    function getCountryById(?PDO $pdo, int $id): ?Country
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchObject($this->className);
    }

    function getCountryByName(?PDO $pdo, string $name): ?Country
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE name = :name");
        $request->execute(array('name' => $name));
        return $request->fetchObject($this->className);
    }

    function getCountryByTag(?PDO $pdo, string $tag): ?Country
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE tag = :tag");
        $request->execute(array('tag' => $tag));
        return $request->fetchObject($this->className);
    }
}
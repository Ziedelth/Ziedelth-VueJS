<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class LangType extends JObject
{
    public int $id;
    public string $name;
    public string $fr;
}

class LangTypeMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.lang_types', 'LangType');
    }

    function getLangTypeById(?PDO $pdo, $id): ?LangType
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchObject($this->className);
    }
}
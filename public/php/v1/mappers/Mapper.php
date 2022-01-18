<?php

abstract class Mapper
{
    public string $tableName;
    protected ?string $className;

    /**
     * @param string $tableName
     * @param ?string $className
     */
    public function __construct(string $tableName, ?string $className = null)
    {
        $this->tableName = $tableName;
        $this->className = $className;
    }
}
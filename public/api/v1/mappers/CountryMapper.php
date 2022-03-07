<?php

class CountryMapper
{
    /**
     * @param PDO $pdo
     * @return array|false
     */
    static function getAllCountries(PDO $pdo)
    {
        $request = $pdo->prepare("SELECT * FROM jais.countries");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
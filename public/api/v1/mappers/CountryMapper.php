<?php

class CountryMapper
{
    /**
     * This function returns all the countries in the database
     *
     * @param PDO $pdo The PDO object that we created earlier.
     *
     * @return array|false An array of associative arrays.
     */
    static function getAllCountries(PDO $pdo)
    {
        $request = $pdo->prepare("SELECT * FROM jais.countries");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
<?php

function getPDO($type): ?PDO
{
    switch ($type) {
        case 'jais':
            return newPDO("jais", "root", "");
        case 'ziedelth':
            return newPDO("ziedelth", "root", "");
        default:
            return null;
    }
}

/**
 * @param string $dbname
 * @param string $dbuser
 * @param string $dbpass
 * @return PDO
 */
function newPDO(string $dbname, string $dbuser, string $dbpass): PDO
{
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
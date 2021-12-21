<?php

function getPDO(): PDO
{
    return newPDO("root", "");
}

/**
 * @param string $dbuser
 * @param string $dbpass
 * @return PDO
 */
function newPDO(string $dbuser, string $dbpass): PDO
{
    $pdo = new PDO("mysql:host=127.0.0.1", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
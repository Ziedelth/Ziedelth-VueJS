<?php

function getPDO(): PDO
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "ziedelth";
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
<?php

function getPDO(): PDO
{
    $pdo = new PDO("mysql:host=127.0.0.1", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    return $pdo;
}

function getCountDelay(PDO $pdo, string $table, int $delayInMonth)
{
    $request = $pdo->prepare("SELECT COUNT(id) AS count FROM $table WHERE STR_TO_DATE(release_date,'%Y-%m-%dT%TZ') > NOW() - INTERVAL $delayInMonth MONTH");
    $request->execute(array());
    return $request->fetch(PDO::FETCH_ASSOC)['count'];
}

$pdo = getPDO();

print_r(getCountDelay($pdo, 'jais.animes', 1));
echo "\n";
print_r(getCountDelay($pdo, 'jais.animes', 3));
echo "\n";
echo "\n";
print_r(getCountDelay($pdo, 'jais.episodes', 1));
echo "\n";
print_r(getCountDelay($pdo, 'jais.episodes', 3));
echo "\n";
echo "\n";
print_r(getCountDelay($pdo, 'jais.scans', 1));
echo "\n";
print_r(getCountDelay($pdo, 'jais.scans', 3));
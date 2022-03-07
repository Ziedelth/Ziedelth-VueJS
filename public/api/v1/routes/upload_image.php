<?php

include_once "../vendor/autoload.php";
include_once "../../configurations/config.php";

include_once "./../templates/EmailTemplate.php";

include_once "./../mappers/EpisodeMapper.php";
include_once "./../mappers/ScanMapper.php";
include_once "./../mappers/AnimeMapper.php";
include_once "./../mappers/CountryMapper.php";

include_once "./../mappers/EmailMapper.php";
include_once "./../mappers/MemberMapper.php";

header('Access-Control-Allow-Origin: *');

/**
 * @return PDO
 */
function getPDO(): PDO
{
    $pdo = new PDO("mysql:host=" . DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    return $pdo;
}

try {
    $pdo = getPDO();

    echo json_encode(MemberMapper::updateImage($pdo, htmlspecialchars(strip_tags($_POST['token'])), $_FILES['file']));
} catch (Exception $exception) {
    echo json_encode(array("error" => "Some shit"));
}
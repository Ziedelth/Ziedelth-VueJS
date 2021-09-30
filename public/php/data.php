<?php

header("Access-Control-Allow-Origin: *");
require_once "database.php";

$country = "France";
if (isset($_GET["country"]) && !empty($_GET["country"])) $country = htmlspecialchars(strip_tags($_GET["country"]));
$pdo = getPDO();

try {
    $request = $pdo->prepare("SELECT COUNT(*) FROM members;");
    $request->execute(array());
    $totalMembers = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM countries;");
    $request->execute(array());
    $totalCountries = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM platforms;");
    $request->execute(array());
    $totalPlatforms = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM animes;");
    $request->execute(array());
    $totalAnimes = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM episodes;");
    $request->execute(array());
    $totalEpisodes = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT SUM(episodes.duration)
FROM episodes
    INNER JOIN platforms ON platforms.id = episodes.platformId
    INNER JOIN countries ON countries.id = episodes.countryId
    INNER JOIN animes ON animes.id = episodes.animeId
WHERE countries.name = :country;");
    $request->execute(array("country" => $country));
    $totalDuration = intval($request->fetch()[0]);

    echo json_encode(array(
        "total_members" => $totalMembers,
        "total_countries" => $totalCountries,
        "total_platforms" => $totalPlatforms,
        "total_animes" => $totalAnimes,
        "total_episodes" => $totalEpisodes,
        "total_duration" => $totalDuration
    ));
} catch (PDOException $ex) {
    echo json_encode(array("error" => $ex));
}


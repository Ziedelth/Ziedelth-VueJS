<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['token'])) {
    $token = htmlspecialchars(strip_tags($json['token']));

    try {
        $database = getPDO();

        if (Utils::isValidAdminToken($database, $token)) {
            $array = [];

            $request = $database->prepare("SELECT (SELECT COUNT(*) FROM jais.episodes) as episodesCount, (SELECT COUNT(*) FROM jais.scans) as scansCount, SUM(duration) as duration FROM jais.episodes;");
            $request->execute(array());
            $totalObjects = $request->fetch(PDO::FETCH_ASSOC);
            $request = $database->prepare("SELECT name, ((SELECT COUNT(*) FROM jais.episodes INNER JOIN jais.animes a on jais.episodes.anime_id = a.id WHERE jais.episodes.platform_id = jais.platforms.id) + (SELECT COUNT(*) FROM jais.scans INNER JOIN jais.animes a on jais.scans.anime_id = a.id WHERE jais.scans.platform_id = jais.platforms.id)) as count, (SELECT SUM(duration) FROM jais.episodes INNER JOIN jais.animes a on jais.episodes.anime_id = a.id WHERE jais.episodes.platform_id = jais.platforms.id) as duration, color FROM jais.platforms");
            $request->execute(array());
            $platformsCount = $request->fetchAll(PDO::FETCH_ASSOC);

            $totalEpisodesCount = $totalObjects['episodesCount'];
            $totalScansCount = $totalObjects['scansCount'];
            $totalDuration = $totalObjects['duration'];
            $array['episodes_count'] = $totalEpisodesCount;
            $array['scans_count'] = $totalScansCount;
            $array['duration'] = $totalDuration;
            $array['platforms'] = $platformsCount;

            http_response_code(201);
            echo json_encode($array);
        } else {
            http_response_code(500);
            echo '{"error":"Invalid token"}';
        }
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
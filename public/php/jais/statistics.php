<?php

include_once '../database.php';
header('Access-Control-Allow-Origin: *');

try {
    $database = getPDO('jais');

    $array = [];

    $request = $database->prepare("SELECT ((SELECT COUNT(*) FROM episodes) + (SELECT COUNT(*) FROM scans)) as count, SUM(duration) as duration FROM episodes;");
    $request->execute(array());
    $totalObjects = $request->fetch(PDO::FETCH_ASSOC);
    $request = $database->prepare("SELECT name, ((SELECT COUNT(*) FROM episodes INNER JOIN animes a on episodes.anime_id = a.id WHERE episodes.platform_id = platforms.id) + (SELECT COUNT(*) FROM scans INNER JOIN animes a on scans.anime_id = a.id WHERE scans.platform_id = platforms.id)) as count, (SELECT SUM(duration) FROM episodes INNER JOIN animes a on episodes.anime_id = a.id WHERE episodes.platform_id = platforms.id) as duration, color FROM platforms");
    $request->execute(array());
    $platformsCount = $request->fetchAll(PDO::FETCH_ASSOC);

    $totalCount = $totalObjects['count'];
    $totalDuration = $totalObjects['duration'];
    $array['count'] = $totalCount;
    $array['duration'] = $totalDuration;
    $array['platforms'] = $platformsCount;

    http_response_code(201);
    echo json_encode($array);
} catch (Exception $exception) {
    http_response_code(500);
    echo '{"error":"' . $exception->getMessage() . '"}';
}
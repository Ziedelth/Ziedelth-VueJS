<?php

include_once 'database.php';
header('Access-Control-Allow-Origin: *');

try {
    $database = getPDO();

    $array = [];

    $request = $database->query("SELECT ((SELECT COUNT(*) FROM episodes) + (SELECT COUNT(*) FROM scans)) as count, SUM(duration) as duration FROM episodes;");
    $request->execute();
    $totalObjects = $request->fetch(PDO::FETCH_ASSOC);
    $request = $database->query("SELECT name, ((SELECT COUNT(*) FROM episodes INNER JOIN animes a on episodes.anime_id = a.id WHERE a.platform_id = platforms.id) + (SELECT COUNT(*) FROM scans INNER JOIN animes a on scans.anime_id = a.id WHERE a.platform_id = platforms.id)) as count, (SELECT SUM(duration) FROM episodes INNER JOIN animes a on episodes.anime_id = a.id WHERE a.platform_id = platforms.id) as duration FROM platforms");
    $request->execute();
    $platformsCount = $request->fetchAll(PDO::FETCH_ASSOC);

    $totalCount = $totalObjects['count'];
    $totalDuration = $totalObjects['duration'];
    $array['count'] = $totalCount;
    $array['duration'] = $totalDuration;
    $array['platforms'] = $platformsCount;

    $history = [];

    for ($i = 0; $i < 30; $i++) {
        $day = date('Y-m-d', strtotime('-' . $i . ' days'));
        $request = $database->query("SELECT ((SELECT COUNT(*) FROM episodes WHERE STR_TO_DATE(release_date, '%Y-%m-%d') = '$day') + (SELECT COUNT(*) FROM scans WHERE STR_TO_DATE(release_date, '%Y-%m-%d') = '$day')) as count, SUM(duration) as duration FROM episodes WHERE STR_TO_DATE(release_date, '%Y-%m-%d') = '$day'");
        $request->execute();
        $dayTotalObject = $request->fetch(PDO::FETCH_ASSOC);
        $dayTotalCount = $dayTotalObject['count'];
        $dayTotalDuration = $dayTotalObject['duration'];
        $request = $database->query("SELECT name, ((SELECT COUNT(*) FROM episodes INNER JOIN animes a on episodes.anime_id = a.id WHERE a.platform_id = platforms.id AND STR_TO_DATE(episodes.release_date, '%Y-%m-%d') = '$day') + (SELECT COUNT(*) FROM scans INNER JOIN animes a on scans.anime_id = a.id WHERE a.platform_id = platforms.id AND STR_TO_DATE(scans.release_date, '%Y-%m-%d') = '$day')) as count, (SELECT SUM(duration) FROM episodes INNER JOIN animes a on episodes.anime_id = a.id WHERE a.platform_id = platforms.id AND STR_TO_DATE(episodes.release_date, '%Y-%m-%d') = '$day') as duration FROM platforms");
        $request->execute();
        $dayPlatformsCount = $request->fetchAll(PDO::FETCH_ASSOC);

        $a = [];
        $a['date'] = $day;
        $a['count'] = $dayTotalCount;
        $a['duration'] = $dayTotalDuration;
        $a['platforms'] = $dayPlatformsCount;

        $history[] = $a;
    }

    $array['history'] = $history;

    http_response_code(201);
    echo json_encode($array);
} catch (Exception $exception) {
    http_response_code(500);
    echo '{"code":"' . http_response_code() . '","error":"' . $exception->getMessage() . '"}';
}
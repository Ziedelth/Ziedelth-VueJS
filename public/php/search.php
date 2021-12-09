<?php

include_once 'database.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['text'])) {
    $text = htmlspecialchars(strip_tags($_GET['text']));

    try {
        $database = getPDO();

        $request = $database->query("SELECT * FROM animes WHERE name LIKE '%$text%' ORDER BY name");
        $request->execute();
        $animes = $request->fetchAll(PDO::FETCH_ASSOC);

        $array = [];

        foreach ($animes as $anime) {
            $platformId = $anime['platform_id'];
            $animeId = $anime['id'];

            $request = $database->query("SELECT * FROM platforms WHERE id = '$platformId'");
            $request->execute();
            $anime['platform'] = $request->fetch(PDO::FETCH_ASSOC);

            $request = $database->query("SELECT COUNT(*) as count, SUM(duration) as duration FROM episodes WHERE anime_id = '$animeId'");
            $request->execute();
            $episodes = $request->fetch(PDO::FETCH_ASSOC);
            $anime['episodes'] = $episodes['count'];
            $anime['duration'] = $episodes['duration'];

            $request = $database->query("SELECT * FROM genres WHERE anime_id = '$animeId'");
            $request->execute();
            $anime['genres'] = $request->fetchAll(PDO::FETCH_ASSOC);

            $array[] = $anime;
        }

        http_response_code(201);
        echo json_encode($array);
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"code":"' . http_response_code() . '","error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo json_encode(array());
}
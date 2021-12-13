<?php

include_once '../database.php';
header('Access-Control-Allow-Origin: *');
$limit = !empty($_GET['limit']) ? intval(htmlspecialchars(strip_tags($_GET['limit']))) : 9;

try {
    $database = getPDO('jais');

    $request = $database->prepare("SELECT * FROM animes ORDER BY name LIMIT $limit;");
    $request->execute(array());
    $animes = $request->fetchAll(PDO::FETCH_ASSOC);

    $array = [];

    foreach ($animes as $anime) {
        $animeId = $anime['id'];

        $request = $database->prepare("SELECT * FROM platforms WHERE id IN (SELECT DISTINCT platform_id FROM episodes WHERE anime_id = :id) OR id IN (SELECT DISTINCT platform_id FROM scans WHERE anime_id = :id)");
        $request->execute(array('id' => $animeId));
        $anime['platforms'] = $request->fetchAll(PDO::FETCH_ASSOC);

        $request = $database->prepare("SELECT DISTINCT season, number, lang_type, episode_type FROM episodes WHERE anime_id = :anime_id ORDER BY episode_type, lang_type, season, number;");
        $request->execute(array('anime_id' => $animeId));
        $anime['episodes'] = $request->rowCount();
        $request = $database->prepare("SELECT DISTINCT number, lang_type, episode_type FROM scans WHERE anime_id = :anime_id ORDER BY episode_type, lang_type, number;");
        $request->execute(array('anime_id' => $animeId));
        $anime['scans'] = $request->rowCount();

        $request = $database->prepare("SELECT name FROM genres WHERE id IN (SELECT genre_id FROM anime_genres WHERE anime_id = :anime_id) ORDER BY name;");
        $request->execute(array('anime_id' => $animeId));
        $genres = [];

        foreach ($request->fetchAll(PDO::FETCH_ASSOC) as $genre) {
            $genres[] = $genre['name'];
        }

        $anime['genres'] = $genres;
        $array[] = $anime;
    }

    http_response_code(201);
    echo json_encode($array);
} catch (Exception $exception) {
    http_response_code(500);
    echo '{"error":"' . $exception->getMessage() . '"}';
}
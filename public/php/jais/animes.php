<?php

include_once '../database.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['limit'])) {
    $limit = intval(htmlspecialchars(strip_tags($_GET['limit'])));

    try {
        $database = getPDO();

        $request = $database->prepare("SELECT * FROM jais.animes ORDER BY name LIMIT $limit;");
        $request->execute(array());
        $animes = $request->fetchAll(PDO::FETCH_ASSOC);

        $array = [];

        foreach ($animes as $anime) {
            $animeId = $anime['id'];

            $request = $database->prepare("SELECT * FROM jais.platforms WHERE id IN (SELECT DISTINCT platform_id FROM jais.episodes WHERE anime_id = :id) OR id IN (SELECT DISTINCT platform_id FROM jais.scans WHERE anime_id = :id)");
            $request->execute(array('id' => $animeId));
            $anime['platforms'] = $request->fetchAll(PDO::FETCH_ASSOC);

            $request = $database->prepare("SELECT DISTINCT season, number, lang_type, episode_type FROM jais.episodes WHERE anime_id = :anime_id ORDER BY episode_type, lang_type, season, number;");
            $request->execute(array('anime_id' => $animeId));
            $anime['episodes'] = $request->rowCount();
            $request = $database->prepare("SELECT DISTINCT number, lang_type, episode_type FROM jais.scans WHERE anime_id = :anime_id ORDER BY episode_type, lang_type, number;");
            $request->execute(array('anime_id' => $animeId));
            $anime['scans'] = $request->rowCount();

            $request = $database->prepare("SELECT * FROM jais.genres WHERE id IN (SELECT genre_id FROM jais.anime_genres WHERE anime_id = :anime_id) ORDER BY name;");
            $request->execute(array('anime_id' => $animeId));
            $genres = [];

            foreach ($request->fetchAll(PDO::FETCH_ASSOC) as $genre) {
                $genres[] = $genre;
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
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
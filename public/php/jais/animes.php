<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['country'])) {
    $country = htmlspecialchars(strip_tags($_GET['country']));

    try {
        $database = getPDO();

        if (Utils::isValidCountry($database, $country)) {
            $request = $database->prepare("SELECT * 
FROM jais.animes a
INNER JOIN countries c on a.country_id = c.id
WHERE c.tag = :country_tag
ORDER BY name;");
            $request->execute(array('country_tag' => $country));
            $animes = $request->fetchAll(PDO::FETCH_ASSOC);

            $array = [];

            foreach ($animes as $anime) {
                $animeId = $anime['id'];

                $request = $database->prepare("SELECT * FROM jais.platforms WHERE id IN (SELECT DISTINCT platform_id FROM jais.episodes WHERE anime_id = $animeId) OR id IN (SELECT DISTINCT platform_id FROM jais.scans WHERE anime_id = $animeId)");
                $request->execute(array());
                $anime['platforms'] = $request->fetchAll(PDO::FETCH_ASSOC);

                $request = $database->prepare("SELECT DISTINCT season, number, lang_type, episode_type FROM jais.episodes WHERE anime_id = :anime_id ORDER BY episode_type, lang_type, season, number;");
                $request->execute(array('anime_id' => $animeId));
                $anime['episodes'] = $request->rowCount();
                $request = $database->prepare("SELECT DISTINCT number, lang_type, episode_type FROM jais.scans WHERE anime_id = :anime_id ORDER BY episode_type, lang_type, number;");
                $request->execute(array('anime_id' => $animeId));
                $anime['scans'] = $request->rowCount();

                $request = $database->prepare("SELECT * FROM jais.genres WHERE id IN (SELECT genre_id FROM jais.anime_genres WHERE anime_id = :anime_id) ORDER BY name;");
                $request->execute(array('anime_id' => $animeId));

                $anime['genres'] = $request->fetchAll(PDO::FETCH_ASSOC);
                $array[] = $anime;
            }

            http_response_code(201);
            echo json_encode($array);
        } else {
            http_response_code(500);
            echo '{"error":"No valid country"}';
        }
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error_message":"' . $exception->getMessage() . '","error_file":"' . $exception->getFile() . '","error_line":"' . $exception->getLine() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
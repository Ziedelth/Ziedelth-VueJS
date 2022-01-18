<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['country'])) {
    $country = htmlspecialchars(strip_tags($_GET['country']));

    try {
        $database = getPDO();

        if (($oCountry = Utils::isValidCountry($database, $country)) != null) {
            $request = $database->prepare("SELECT a.*
FROM jais.animes a
INNER JOIN jais.countries c on a.country_id = c.id
WHERE c.tag = :country_tag
ORDER BY a.name;");
            $request->execute(array('country_tag' => $country));
            $animes = $request->fetchAll(PDO::FETCH_ASSOC);

            $array = [];

            foreach ($animes as $anime) {
                $animeId = $anime['id'];

                $request = $database->prepare("SELECT * FROM jais.platforms WHERE id IN (SELECT DISTINCT platform_id FROM jais.episodes WHERE anime_id = $animeId) OR id IN (SELECT DISTINCT platform_id FROM jais.scans WHERE anime_id = $animeId)");
                $request->execute(array());
                $anime['platforms'] = $request->fetchAll(PDO::FETCH_ASSOC);

                $request = $database->prepare("SELECT DISTINCT e.id_episode_type, e.id_lang_type, e.season, e.number, CONCAT(et.$country, ' ', e.number, ' ', lt.$country) as short_resume
FROM jais.episodes e
INNER JOIN jais.animes a on e.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.episode_types et ON e.id_episode_type = et.id INNER JOIN jais.lang_types lt ON e.id_lang_type = lt.id
WHERE e.anime_id = :anime_id
ORDER BY e.id_episode_type, e.season, e.number, e.id_lang_type;");
                $request->execute(array('anime_id' => $animeId));
                $count = $request->rowCount();
                $episodes = $request->fetchAll(PDO::FETCH_ASSOC);
                $aEpisodes = [];

                $anime['episodesSize'] = $count;

                foreach ($episodes as $episode) {
                    $aEpisodes[$episode['season']][] = $episode;
                }

                $anime['episodes'] = $aEpisodes;

                $request = $database->prepare("SELECT DISTINCT s.id_episode_type, s.id_lang_type, s.number,  CONCAT(et.$country, ' ', s.number, ' ', lt.$country) as short_resume
FROM jais.scans s
INNER JOIN jais.animes a on s.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on s.platform_id = p.id INNER JOIN jais.episode_types et on s.id_episode_type = et.id INNER JOIN jais.lang_types lt on s.id_lang_type = lt.id 
WHERE s.anime_id = :anime_id 
ORDER BY s.id_episode_type, s.id_lang_type, s.number;");
                $request->execute(array('anime_id' => $animeId));
                $count = $request->rowCount();
                $scans = $request->fetchAll(PDO::FETCH_ASSOC);

                $anime['scansSize'] = $count;
                $anime['scans'] = $scans;

                $request = $database->prepare("SELECT $country FROM jais.genres WHERE id IN (SELECT genre_id FROM jais.anime_genres WHERE anime_id = :anime_id) ORDER BY name;");
                $request->execute(array('anime_id' => $animeId));

                $anime['genres'] = array_map(function ($array) use ($country) {
                    return $array[$country];
                }, $request->fetchAll(PDO::FETCH_ASSOC));

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
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
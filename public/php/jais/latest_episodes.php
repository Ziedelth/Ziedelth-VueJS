<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');


if (!empty($_GET['limit'])) {
    $limit = intval(htmlspecialchars(strip_tags($_GET['limit'])));

    try {
        $database = getPDO();
        $request = $database->prepare("SELECT jais.episodes.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, episodes.episode_id, episodes.release_date, episodes.season, episodes.number, episodes.episode_type, episodes.lang_type, episodes.title, episodes.url as episode_url, episodes.image as episode_image, episodes.duration 
FROM jais.episodes INNER JOIN jais.animes a on jais.episodes.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on episodes.platform_id = p.id 
ORDER BY STR_TO_DATE(jais.episodes.release_date, '%Y-%m-%dT%TZ') DESC, a.name, jais.episodes.season DESC, jais.episodes.number DESC, jais.episodes.episode_type, jais.episodes.lang_type 
LIMIT $limit;");
        $request->execute(array());
        $episodes = $request->fetchAll(PDO::FETCH_ASSOC);
        $array = [];

        foreach ($episodes as $episode) {
            $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.checks WHERE episode_id = :episode_id)");
            $request->execute(array('episode_id' => $episode['id']));
            $checks = array_map('map', $request->fetchAll(PDO::FETCH_ASSOC));
            $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.loves WHERE anime_id = :anime_id)");
            $request->execute(array('anime_id' => $episode['anime_id']));
            $loves = array_map('map', $request->fetchAll(PDO::FETCH_ASSOC));

            unset($episode['id']);
            $episode['checks'] = $checks;
            $episode['loves'] = $loves;
            $array[] = $episode;
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

function map($n): string
{
    return $n['pseudo'];
}
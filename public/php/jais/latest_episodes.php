<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['country']) && !empty($_GET['limit'])) {
    $country = htmlspecialchars(strip_tags($_GET['country']));
    $limit = intval(htmlspecialchars(strip_tags($_GET['limit'])));

    try {
        $database = getPDO();

        if (Utils::isValidCountry($database, $country)) {
            $request = $database->prepare("SELECT e.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, e.episode_id, e.release_date, CONCAT(c.season, ' ', e.season, ' • ', et.$country, ' ', e.number, ' ', lt.$country) as resume, e.season, e.number, et.$country as episode_type, e.title, e.url as episode_url, e.image as episode_image, e.duration 
FROM jais.episodes e
INNER JOIN jais.animes a on e.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on e.platform_id = p.id INNER JOIN jais.episode_types et on e.id_episode_type = et.id INNER JOIN jais.lang_types lt on e.id_lang_type = lt.id 
WHERE c.tag = :country_tag
ORDER BY STR_TO_DATE(e.release_date, '%Y-%m-%dT%TZ') DESC, a.name, e.season DESC, e.number DESC, et.name, lt.name 
LIMIT $limit;");
            $request->execute(array('country_tag' => $country));
            $episodes = $request->fetchAll(PDO::FETCH_ASSOC);
            $array = [];

            foreach ($episodes as $episode) {
                $checks = [];
                $loves = [];

                try {
                    $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.checks WHERE episode_id = :episode_id)");
                    $request->execute(array('episode_id' => $episode['id']));
                    $checks = array_map(function ($array) {
                        return;
                    }, $request->fetchAll(PDO::FETCH_ASSOC));
                    $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.loves WHERE anime_id = :anime_id)");
                    $request->execute(array('anime_id' => $episode['anime_id']));
                    $loves = array_map(function ($array) {
                        return $array['pseudo'];
                    }, $request->fetchAll(PDO::FETCH_ASSOC));
                } catch (PDOException $exception) {

                }

                unset($episode['id']);
                $episode['checks'] = $checks;
                $episode['loves'] = $loves;
                $array[] = $episode;
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
<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['token'])) {
    try {
        $token = htmlspecialchars(strip_tags($json['token']));
        $database = getPDO();

        if (($user = Utils::isValidToken($database, $token)) != null) {
            if (!empty($json['episode_id']) && ($episode = Utils::isValidEpisodeId($database, htmlspecialchars(strip_tags($json['episode_id'])))) != null)
                Utils::checkEpisode($database, $user, $episode);
            if (!empty($json['anime_id']) && ($anime = Utils::isValidAnimeId($database, $animeId = intval(htmlspecialchars(strip_tags($json['anime_id']))))) != null)
                Utils::loveAnime($database, $user, $anime);

            http_response_code(201);
            echo '{"text":"ok"}';
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
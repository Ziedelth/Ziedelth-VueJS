<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['token']) && !empty($json['episode_id']) && !empty($json['id_episode_type']) && !empty($json['season']) && !empty($json['number']) && !empty($json['duration'])) {
    $token = htmlspecialchars(strip_tags($json['token']));
    $episodeId = htmlspecialchars(strip_tags($json['episode_id']));
    $idEpisodeType = intval(htmlspecialchars(strip_tags($json['id_episode_type'])));
    $season = intval(htmlspecialchars(strip_tags($json['season'])));
    $number = intval(htmlspecialchars(strip_tags($json['number'])));
    $title = htmlspecialchars(strip_tags($json['title']));
    $duration = intval(htmlspecialchars(strip_tags($json['duration'])));

    try {
        $database = getPDO();

        if (($user = Utils::isValidAdminToken($database, $token)) != null) {
            if (($episode = Utils::isValidEpisodeId($database, $episodeId)) != null) {
                $request = $database->prepare("UPDATE jais.episodes SET id_episode_type = :id_episode_type, season = :season, number = :number, title = :title, duration = :duration WHERE episode_id = :episode_id");
                $request->execute(array('episode_id' => $episodeId, 'id_episode_type' => $idEpisodeType, 'season' => $season, 'number' => $number, 'title' => (empty($title) || $title == 'null') ? null : $title, 'duration' => $duration));

                http_response_code(201);
                echo '{"text":"ok"}';
            } else {
                http_response_code(500);
                echo '{"error":"Invalid anime id"}';
            }
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
<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['token']) && !empty($json['episode_id'])) {
    $token = htmlspecialchars(strip_tags($json['token']));
    $episodeId = htmlspecialchars(strip_tags($json['episode_id']));

    try {
        $database = getPDO();

        if (($user = Utils::isValidToken($database, $token)) != null) {
            if (($episode = Utils::isValidEpisodeId($database, $episodeId)) != null) {
                $request = $database->prepare("SELECT * FROM ziedelth.checks WHERE user_id = :user_id AND episode_id = :episode_id");
                $request->execute(array('user_id' => $user['id'], 'episode_id' => $episode['id']));
                $rows = $request->rowCount();

                if ($rows >= 1)
                    $request = $database->prepare("DELETE FROM ziedelth.checks WHERE user_id = :user_id AND episode_id = :episode_id");
                else
                    $request = $database->prepare("INSERT INTO ziedelth.checks (id, timestamp, user_id, episode_id) VALUES (NULL, CURRENT_TIMESTAMP(), :user_id, :episode_id)");
                $request->execute(array('user_id' => $user['id'], 'episode_id' => $episode['id']));
                http_response_code(201);
                echo '{"text":"ok"}';
            } else {
                http_response_code(500);
                echo '{"error":"Invalid episode id"}';
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
<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['token']) && !empty($json['anime_id'])) {
    $token = htmlspecialchars(strip_tags($json['token']));
    $animeId = intval(htmlspecialchars(strip_tags($json['anime_id'])));

    try {
        $database = getPDO();

        if (($user = Utils::isValidToken($database, $token)) != null) {
            if (($anime = Utils::isValidAnimeId($database, $animeId)) != null) {
                $request = $database->prepare("SELECT * FROM ziedelth.loves WHERE user_id = :user_id AND anime_id = :anime_id");
                $request->execute(array('user_id' => $user['id'], 'anime_id' => $anime['id']));
                $rows = $request->rowCount();

                if ($rows >= 1)
                    $request = $database->prepare("DELETE FROM ziedelth.loves WHERE user_id = :user_id AND anime_id = :anime_id");
                else
                    $request = $database->prepare("INSERT INTO ziedelth.loves (id, timestamp, user_id, anime_id) VALUES (NULL, CURRENT_TIMESTAMP(), :user_id, :anime_id)");
                $request->execute(array('user_id' => $user['id'], 'anime_id' => $anime['id']));
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
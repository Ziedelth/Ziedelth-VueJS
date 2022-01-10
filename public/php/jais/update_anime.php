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

        if (($user = Utils::isValidAdminToken($database, $token)) != null) {
            if (($episode = Utils::isValidAnimeId($database, $animeId)) != null) {
                if (!empty($json['genres'])) {
                    $request = $database->prepare("DELETE FROM jais.anime_genres WHERE anime_id = :anime_id");
                    $request->execute(array('anime_id' => $animeId));

                    foreach ($json['genres'] as $genreId) {
                        $request = $database->prepare("INSERT INTO jais.anime_genres (anime_id, genre_id) VALUES (:anime_id, :genre_id)");
                        $request->execute(array('anime_id' => $animeId, 'genre_id' => intval(htmlspecialchars(strip_tags($genreId)))));
                    }
                }

                if (!empty($json['description'])) {
                    $request = $database->prepare("UPDATE jais.animes SET description = :description WHERE id = :anime_id");
                    $request->execute(array('anime_id' => $animeId, 'description' => htmlspecialchars(strip_tags($json['description']))));
                }

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
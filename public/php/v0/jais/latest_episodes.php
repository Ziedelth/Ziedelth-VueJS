<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['country']) && !empty($_GET['limit'])) {
    $country = htmlspecialchars(strip_tags($_GET['country']));
    $limit = intval(htmlspecialchars(strip_tags($_GET['limit'])));

    try {
        $database = getPDO();

        http_response_code(201);
        echo json_encode(Utils::getLatestEpisodes($database, $country, $limit));
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
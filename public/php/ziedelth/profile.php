<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['pseudo'])) {
    try {
        $database = getPDO();
        $pseudo = htmlspecialchars(strip_tags($_GET['pseudo']));

        echo json_encode(Utils::getProfile($database, $pseudo));
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
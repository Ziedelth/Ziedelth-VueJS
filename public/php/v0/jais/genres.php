<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');

try {
    $database = getPDO();

    $request = $database->prepare("SELECT * FROM jais.genres;");
    $request->execute(array());

    http_response_code(201);
    echo json_encode($request->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $exception) {
    http_response_code(500);
    echo '{"error":"' . $exception->getMessage() . '"}';
}
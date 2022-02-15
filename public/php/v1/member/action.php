<?php

require_once "../autoload.php";
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

try {
    $userMapper = new UserMapper();
    $pdo = Utils::getPDO();

    if (!empty($json['hash']))
        Utils::printResponse($userMapper->action($pdo, htmlspecialchars(strip_tags($json['hash']))));
    else
        Utils::printResponse(new JSONResponse(400, array('error' => 'Bad format')));
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
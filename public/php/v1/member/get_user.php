<?php

require_once "../autoload.php";
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

try {
    $userMapper = new UserMapper();
    $pdo = Utils::getPDO();

    if (!empty($json['token']))
        Utils::printResponse($userMapper->getUserByToken($pdo, htmlspecialchars(strip_tags($json['token']))));
    elseif (!empty($_GET['pseudo']))
        Utils::printResponse($userMapper->getUserByPseudo($pdo, htmlspecialchars(strip_tags($_GET['pseudo']))));
    else
        Utils::printResponse(new JSONResponse(400, array('error' => 'Bad format')));
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
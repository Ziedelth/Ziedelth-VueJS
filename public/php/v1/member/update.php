<?php

require_once "../autoload.php";
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = Utils::getPDO();
    $userMapper = new UserMapper();

    if (!empty($json['token']))
        Utils::printResponse($userMapper->updateMemberAbout($pdo, htmlspecialchars(strip_tags($json['token'])), htmlspecialchars(strip_tags($json['about']))));
    else
        Utils::printResponse(new JSONResponse(400, array('error' => 'Bad format')));
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
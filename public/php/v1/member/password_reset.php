<?php

require_once "../autoload.php";
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

try {
    if (!empty($json['email'])) {
        $pdo = Utils::getPDO();
        $userMapper = new UserMapper();
        Utils::printResponse($userMapper->passwordReset($pdo, htmlspecialchars(strip_tags($json['email']))));
    } else {
        Utils::printResponse(new JSONResponse(400, array('error' => 'Bad format')));
    }
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
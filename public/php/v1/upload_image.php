<?php

require_once "autoload.php";
header('Access-Control-Allow-Origin: *');

try {
    $pdo = Utils::getPDO();
    $userMapper = new UserMapper();

    if (!empty($_POST['token']) && !empty($_FILES['file']))
        Utils::printResponse($userMapper->updateMemberImage($pdo, htmlspecialchars(strip_tags($_POST['token'])), $_FILES['file']));
    else
        Utils::printResponse(new JSONResponse(400, array('error' => 'Bad format')));
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
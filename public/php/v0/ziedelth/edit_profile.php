<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

$type = 0;
if (!empty($_GET['type'])) $type = intval(htmlspecialchars(strip_tags($_GET['type'])));

if (($type == 1 && !empty($_POST['token'])) || !empty($json['token'])) {
    try {
        $token = $type == 1 ? htmlspecialchars(strip_tags($_POST['token'])) : htmlspecialchars(strip_tags($json['token']));
        $database = getPDO();

        if (($user = Utils::isValidToken($database, $token)) != null) {
            if (!empty($json['bio']))
                Utils::editBio($database, $user, htmlspecialchars(strip_tags($json['bio'])));

            if ($type == 1) {
                if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                    $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $path = 'images/members/' . Utils::generateRandomString(100) . '.' . $extension;

                    if (in_array($extension, array('jpg', 'png', 'gif')) && Utils::uploadFile($user['image'], $path))
                        Utils::editImage($database, $user, $path);
                }
            }

            http_response_code(201);
            echo json_encode(Utils::getProfile($database, $user['pseudo'], true));
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
<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['pseudo']) && !empty($json['salt_password'])) {
    try {
        $database = getPDO();
        $pseudo = htmlspecialchars(strip_tags($json['pseudo']));
        $saltPassword = htmlspecialchars(strip_tags($json['salt_password']));

        // Pseudo
        if (strlen($pseudo) >= 4 && strlen($pseudo) <= 16) {
            $request = $database->prepare("SELECT * FROM ziedelth.users WHERE pseudo = :pseudo");
            $request->execute(array('pseudo' => $pseudo));
            $rows = $request->rowCount();

            if ($rows == 0) {
                // Password
                if (strpos($saltPassword, '$') !== false && count(explode('$', $saltPassword)) === 2) {
                    $salt = explode('$', $saltPassword)[0];
                    $password = explode('$', $saltPassword)[1];
                    // Generated token
                    $token = generateRandomString(50);

                    $request = $database->prepare("INSERT INTO ziedelth.users (timestamp, pseudo, salt_password, token, image, role, bio) VALUES (CURRENT_TIME, :pseudo, :salt_password, :token, NULL, 0, NULL)");
                    $request->execute(array('pseudo' => $pseudo, 'salt_password' => $saltPassword, 'token' => $token));

                    http_response_code(201);
                    echo '{"status":"ok"}';
                } else {
                    http_response_code(500);
                    echo '{"error":"Password invalid format"}';
                }
            } else {
                http_response_code(500);
                echo '{"error":"Pseudo already used"}';
            }
        } else {
            http_response_code(500);
            echo '{"error":"Pseudo invalid format"}';
        }
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}

function generateRandomString($length = 25): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}
<?php

include_once '../database.php';
include_once '../Utils.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['pseudo']) && !empty($json['password'])) {
    $pseudo = htmlspecialchars(strip_tags($json['pseudo']));
    $password = htmlspecialchars(strip_tags($json['password']));

    try {
        $database = getPDO();

        // Pseudo
        if (strlen($pseudo) >= 4 && strlen($pseudo) <= 16) {
            $request = $database->prepare("SELECT id, timestamp, pseudo, salt_password, token, image, role, bio FROM ziedelth.users WHERE pseudo = :pseudo");
            $request->execute(array('pseudo' => $pseudo));
            $rows = $request->rowCount();

            if ($rows == 1) {
                $user = $request->fetch(PDO::FETCH_ASSOC);
                $saltPassword = $user['salt_password'];
                $salt = explode('$', $saltPassword)[0];
                $saltPassword = explode('$', $saltPassword)[1];

                $hash = hash('sha512', $salt . '||YOU_WIN||' . $password);

                if ($hash === $saltPassword) {
                    Utils::insertIP($user, $database);
                } else {
                    http_response_code(500);
                    echo '{"error":"Bad credentials"}';
                }
            } else {
                http_response_code(500);
                echo '{"error":"Pseudo not used"}';
            }
        } else {
            http_response_code(500);
            echo '{"error":"Pseudo invalid format"}';
        }
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else if (!empty($json['token'])) {
    $token = htmlspecialchars(strip_tags($json['token']));

    try {
        $database = getPDO();

        if (($user = Utils::isValidToken($database, $token)) != null) {
            Utils::insertIP($user, $database);
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
<?php

include_once '../database.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['pseudo']) && !empty($json['email']) && !empty($json['salt_password'])) {
    try {
        $database = getPDO('ziedelth');
        $pseudo = htmlspecialchars(strip_tags($json['pseudo']));
        $email = htmlspecialchars(strip_tags($json['email']));
        $saltPassword = htmlspecialchars(strip_tags($json['salt_password']));

        // Pseudo
        if (strlen($pseudo) >= 4 && strlen($pseudo) <= 16) {
            $request = $database->prepare("SELECT * FROM users WHERE pseudo = :pseudo");
            $request->execute(array('pseudo' => $pseudo));
            $rows = $request->rowCount();

            if ($rows == 0) {
                // Email
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $request = $database->prepare("SELECT * FROM users WHERE email = :email");
                    $request->execute(array('email' => $email));
                    $rows = $request->rowCount();

                    if ($rows == 0) {
                        // Password
                        if (strpos($saltPassword, '$') !== false && count(explode('$', $saltPassword)) === 2) {
                            $salt = explode('$', $saltPassword)[0];
                            $password = explode('$', $saltPassword)[1];
                            // Generated token
                            $token = generateRandomString(50);

                            $request = $database->prepare("INSERT INTO users (timestamp, pseudo, email, salt_password, image, token, role, bio) VALUES (CURRENT_TIME, :pseudo, :email, :salt_password, :token, NULL, 0, NULL)");
                            $request->execute(array('pseudo' => $pseudo, 'email' => $email, 'salt_password' => $saltPassword, 'token' => $token));

                            http_response_code(201);
                            echo '{"status":"ok"}';
                        } else {
                            http_response_code(500);
                            echo '{"error":"Password invalid format"}';
                        }
                    } else {
                        http_response_code(500);
                        echo '{"error":"Email already used"}';
                    }
                } else {
                    http_response_code(500);
                    echo '{"error":"Email invalid format"}';
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
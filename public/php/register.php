<?php

include_once 'database.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['pseudo']) && !empty($json['email']) && !empty($json['salt_password'])) {
    try {
        $database = getPDO();
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

                            $request = $database->prepare("INSERT INTO users (timestamp, pseudo, email, salt_password) VALUES (CURRENT_TIME, :pseudo, :email, :salt_password)");
                            $request->execute(array('pseudo' => $pseudo, 'email' => $email, 'salt_password' => $saltPassword));

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
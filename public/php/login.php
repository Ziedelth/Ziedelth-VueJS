<?php

include_once 'database.php';
header('Access-Control-Allow-Origin: *');
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['pseudo']) && !empty($json['password'])) {
    try {
        $database = getPDO();
        $pseudo = htmlspecialchars(strip_tags($json['pseudo']));
        $password = htmlspecialchars(strip_tags($json['password']));

        // Pseudo
        if (strlen($pseudo) >= 4 && strlen($pseudo) <= 16) {
            $request = $database->prepare("SELECT * FROM users WHERE pseudo = :pseudo");
            $request->execute(array('pseudo' => $pseudo));
            $rows = $request->rowCount();

            if ($rows == 1) {
                $user = $request->fetch(PDO::FETCH_ASSOC);
                $saltPassword = $user['salt_password'];
                $salt = explode('$', $saltPassword)[0];
                $saltPassword = explode('$', $saltPassword)[1];

                $hash = hash('sha512', $salt . '||YOU_WIN||' . $password);

                if ($hash === $saltPassword) {
                    // IP
                    $userId = $user['id'];
                    $ip = $_SERVER['REMOTE_ADDR'];

                    try {
                        $request = $database->prepare("SELECT * FROM users_ip WHERE user_id = :user_id AND ip = :ip");
                        $request->execute(array('user_id' => $userId, 'ip' => $ip));
                        $rows = $request->rowCount();

                        if ($rows == 0) {
                            $request = $database->prepare("INSERT INTO users_ip (user_id, ip) VALUES (:user_id, :ip)");
                            $request->execute(array('user_id' => $userId, 'ip' => $ip));
                        }
                    } catch (Exception $exception) {

                    }

                    http_response_code(201);
                    echo '{"status":"Ok"}';
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
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
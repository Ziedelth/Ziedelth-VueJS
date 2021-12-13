<?php

include_once '../database.php';
header('Access-Control-Allow-Origin: *');

if (!empty($_GET['pseudo'])) {
    try {
        $database = getPDO('ziedelth');
        $pseudo = htmlspecialchars(strip_tags($_GET['pseudo']));

        $request = $database->prepare("SELECT pseudo, role, bio FROM users WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $rows = $request->rowCount();

        if ($rows == 1) {
            $user = $request->fetch(PDO::FETCH_ASSOC);
            http_response_code(201);
            echo json_encode($user);
        } else {
            http_response_code(500);
            echo '{"error":"Pseudo not used"}';
        }
    } catch (Exception $exception) {
        http_response_code(500);
        echo '{"error":"' . $exception->getMessage() . '"}';
    }
} else {
    http_response_code(500);
    echo '{"error":"Invalid format"}';
}
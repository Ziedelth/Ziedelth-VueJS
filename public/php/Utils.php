<?php

class Utils
{
    static function isValidToken(PDO $database, $token)
    {
        try {
            $request = $database->prepare("SELECT * FROM ziedelth.users WHERE token = :token");
            $request->execute(array('token' => $token));
            $rows = $request->rowCount();
            return $rows >= 1 ? $request->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $exception) {
            return null;
        }
    }

    static function isValidAdminToken(PDO $database, $token)
    {
        try {
            $request = $database->prepare("SELECT * FROM ziedelth.users WHERE token = :token AND role = 100");
            $request->execute(array('token' => $token));
            $rows = $request->rowCount();
            return $rows >= 1 ? $request->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $exception) {
            return null;
        }
    }

    static function isValidEpisodeId(PDO $database, $episodeId)
    {
        try {
            $request = $database->prepare("SELECT * FROM jais.episodes WHERE episode_id = :episode_id");
            $request->execute(array('episode_id' => $episodeId));
            $rows = $request->rowCount();
            return $rows >= 1 ? $request->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $exception) {
            return null;
        }
    }

    static function isValidAnimeId(PDO $database, $animeId)
    {
        try {
            $request = $database->prepare("SELECT * FROM jais.animes WHERE id = :id");
            $request->execute(array('id' => $animeId));
            $rows = $request->rowCount();
            return $rows >= 1 ? $request->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $exception) {
            return null;
        }
    }

    static function getPseudo(PDO $database, $userId)
    {
        try {
            $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id = :id");
            $request->execute(array('id' => $userId));
            $rows = $request->rowCount();
            return $rows >= 1 ? $request->fetch(PDO::FETCH_ASSOC)['pseudo'] : null;
        } catch (Exception $exception) {
            return null;
        }
    }

    static /**
     * @param $user
     * @param PDO|null $database
     */
    function insertIP($user, ?PDO $database)
    {
        $userId = $user['id'];
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            $request = $database->prepare("SELECT * FROM ziedelth.users_ip WHERE user_id = :user_id AND ip = :ip");
            $request->execute(array('user_id' => $userId, 'ip' => $ip));
            $rows = $request->rowCount();

            if ($rows == 0)
                $request = $database->prepare("INSERT INTO ziedelth.users_ip (timestamp, user_id, ip) VALUES (CURRENT_TIMESTAMP(), :user_id, :ip)");
            else
                $request = $database->prepare("UPDATE ziedelth.users_ip SET timestamp = CURRENT_TIMESTAMP() WHERE user_id = :user_id AND ip = :ip");
            $request->execute(array('user_id' => $userId, 'ip' => $ip));
        } catch (Exception $exception) {

        }

        unset($user['id']);
        unset($user['email']);
        unset($user['salt_password']);
        http_response_code(201);
        echo json_encode($user);
    }
}
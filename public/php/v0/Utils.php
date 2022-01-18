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

    static function getProfile(?PDO $database, $country, $pseudo, bool $token = false)
    {
        if ($token)
            $request = $database->prepare("SELECT id, timestamp, pseudo, token, image, role, bio FROM ziedelth.users WHERE pseudo = :pseudo");
        else
            $request = $database->prepare("SELECT id, timestamp, pseudo, image, role, bio FROM ziedelth.users WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $user = $request->fetch(PDO::FETCH_ASSOC);

        if ($user !== FALSE) {
            $userId = $user['id'];

            $request = $database->prepare("SELECT episode_id FROM ziedelth.checks WHERE user_id = :user_id");
            $request->execute(array('user_id' => $userId));
            $user['checks'] = self::getEpisodes($database, $country, array_map(function ($array) {
                return $array['episode_id'];
            }, $request->fetchAll(PDO::FETCH_ASSOC)));
        }

        return $user;
    }

    static function getEpisodes(?PDO $database, $country, $ids): array
    {
        $string = join(', ', $ids);
        $request = $database->prepare("SELECT e.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, e.episode_id, e.release_date, CONCAT(c.season, ' ', e.season, ' • ', et.$country, ' ', e.number, ' ', lt.$country) as resume, e.season, e.number, et.$country as episode_type, e.title, e.url as episode_url, e.image as episode_image, e.duration 
FROM jais.episodes e
INNER JOIN jais.animes a on e.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on e.platform_id = p.id INNER JOIN jais.episode_types et on e.id_episode_type = et.id INNER JOIN jais.lang_types lt on e.id_lang_type = lt.id 
WHERE e.id IN ($string)
ORDER BY STR_TO_DATE(e.release_date, '%Y-%m-%dT%TZ') DESC, a.name, e.season DESC, e.number DESC, et.name, lt.name;");
        $request->execute(array());
        $episodes = $request->fetchAll(PDO::FETCH_ASSOC);
        $array = [];

        foreach ($episodes as $episode) {
            $checks = [];
            $loves = [];

            try {
                $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.checks WHERE episode_id = :episode_id)");
                $request->execute(array('episode_id' => $episode['id']));
                $checks = array_map(function ($array) {
                    return $array['pseudo'];
                }, $request->fetchAll(PDO::FETCH_ASSOC));
                $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.loves WHERE anime_id = :anime_id)");
                $request->execute(array('anime_id' => $episode['anime_id']));
                $loves = array_map(function ($array) {
                    return $array['pseudo'];
                }, $request->fetchAll(PDO::FETCH_ASSOC));
            } catch (PDOException $exception) {

            }

            unset($episode['id']);
            $episode['checks'] = $checks;
            $episode['loves'] = $loves;
            $array[] = $episode;
        }

        return $array;
    }

    static function checkEpisode(?PDO $database, $user, $episode)
    {
        $request = $database->prepare("SELECT * FROM ziedelth.checks WHERE user_id = :user_id AND episode_id = :episode_id");
        $request->execute(array('user_id' => $user['id'], 'episode_id' => $episode['id']));
        $rows = $request->rowCount();

        if ($rows >= 1)
            $request = $database->prepare("DELETE FROM ziedelth.checks WHERE user_id = :user_id AND episode_id = :episode_id");
        else
            $request = $database->prepare("INSERT INTO ziedelth.checks (id, timestamp, user_id, episode_id) VALUES (NULL, CURRENT_TIMESTAMP(), :user_id, :episode_id)");
        $request->execute(array('user_id' => $user['id'], 'episode_id' => $episode['id']));
    }

    static function loveAnime(?PDO $database, $user, $anime)
    {
        $request = $database->prepare("SELECT * FROM ziedelth.loves WHERE user_id = :user_id AND anime_id = :anime_id");
        $request->execute(array('user_id' => $user['id'], 'anime_id' => $anime['id']));
        $rows = $request->rowCount();

        if ($rows >= 1)
            $request = $database->prepare("DELETE FROM ziedelth.loves WHERE user_id = :user_id AND anime_id = :anime_id");
        else
            $request = $database->prepare("INSERT INTO ziedelth.loves (id, timestamp, user_id, anime_id) VALUES (NULL, CURRENT_TIMESTAMP(), :user_id, :anime_id)");
        $request->execute(array('user_id' => $user['id'], 'anime_id' => $anime['id']));
    }

    static function editBio(?PDO $database, $user, $newBio)
    {
        $request = $database->prepare("UPDATE ziedelth.users SET bio = :bio WHERE id = :user_id");
        $request->execute(array('user_id' => $user['id'], 'bio' => $newBio));
    }

    static function editImage(?PDO $database, $user, $path)
    {
        $request = $database->prepare("UPDATE ziedelth.users SET image = :image WHERE id = :user_id");
        $request->execute(array('user_id' => $user['id'], 'image' => $path));
    }

    static function generateRandomString($length = 25): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    static function createFolder($folder)
    {
        if (!self::isFileExists($folder))
            mkdir($folder);
    }

    static function isFileExists($file): bool
    {
        return file_exists($file);
    }

    static function getLatestEpisodes(?PDO $database, $country, $limit): array
    {
        if (($oCountry = Utils::isValidCountry($database, $country)) != null) {
            $request = $database->prepare("SELECT e.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, e.episode_id, e.release_date, CONCAT(c.season, ' ', e.season, ' • ', et.$country, ' ', e.number, ' ', lt.$country) as resume, e.season, e.number, et.$country as episode_type, e.title, e.url as episode_url, e.image as episode_image, e.duration 
FROM jais.episodes e
INNER JOIN jais.animes a on e.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on e.platform_id = p.id INNER JOIN jais.episode_types et on e.id_episode_type = et.id INNER JOIN jais.lang_types lt on e.id_lang_type = lt.id 
WHERE c.tag = :country_tag
ORDER BY STR_TO_DATE(e.release_date, '%Y-%m-%dT%TZ') DESC, a.name, e.season DESC, e.number DESC, et.name, lt.name 
LIMIT $limit;");
            $request->execute(array('country_tag' => $country));
            $episodes = $request->fetchAll(PDO::FETCH_ASSOC);
            $array = [];

            foreach ($episodes as $episode) {
                $checks = [];
                $loves = [];

                try {
                    $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.checks WHERE episode_id = :episode_id)");
                    $request->execute(array('episode_id' => $episode['id']));
                    $checks = array_map(function ($array) {
                        return $array['pseudo'];
                    }, $request->fetchAll(PDO::FETCH_ASSOC));
                    $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.loves WHERE anime_id = :anime_id)");
                    $request->execute(array('anime_id' => $episode['anime_id']));
                    $loves = array_map(function ($array) {
                        return $array['pseudo'];
                    }, $request->fetchAll(PDO::FETCH_ASSOC));
                } catch (PDOException $exception) {

                }

                unset($episode['id']);
                $episode['checks'] = $checks;
                $episode['loves'] = $loves;
                $array[] = $episode;
            }

            return $array;
        }

        return array();
    }

    static function isValidCountry(PDO $database, $country)
    {
        try {
            $request = $database->prepare("SELECT * FROM jais.countries WHERE tag = :tag");
            $request->execute(array('tag' => $country));
            $rows = $request->rowCount();
            return $rows >= 1 ? $request->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $exception) {
            return false;
        }
    }

    static function getLatestScans(?PDO $database, $country, $limit): array
    {
        if (($oCountry = Utils::isValidCountry($database, $country)) != null) {
            $request = $database->prepare("SELECT s.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, a.image as anime_image, s.release_date, CONCAT(et.$country, ' ', s.number, ' ', lt.$country) as resume, s.number, et.$country as scan_type, s.url as scan_url
FROM jais.scans s
INNER JOIN jais.animes a on s.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on s.platform_id = p.id INNER JOIN jais.episode_types et on s.id_episode_type = et.id INNER JOIN jais.lang_types lt on s.id_lang_type = lt.id 
WHERE c.tag = :country_tag
ORDER BY STR_TO_DATE(s.release_date, '%Y-%m-%dT%TZ') DESC, a.name, s.number DESC, et.name, lt.name 
LIMIT $limit;");
            $request->execute(array('country_tag' => $country));
            $scans = $request->fetchAll(PDO::FETCH_ASSOC);
            $array = [];

            foreach ($scans as $scan) {
                $checks = [];
                $loves = [];

                try {
//                    $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.checks WHERE episode_id = :episode_id)");
//                    $request->execute(array('episode_id' => $episode['id']));
//                    $checks = array_map(function ($array) {
//                        return;
//                    }, $request->fetchAll(PDO::FETCH_ASSOC));
                    $request = $database->prepare("SELECT pseudo FROM ziedelth.users WHERE id IN (SELECT user_id FROM ziedelth.loves WHERE anime_id = :anime_id)");
                    $request->execute(array('anime_id' => $scan['anime_id']));
                    $loves = array_map(function ($array) {
                        return $array['pseudo'];
                    }, $request->fetchAll(PDO::FETCH_ASSOC));
                } catch (PDOException $exception) {

                }

                unset($scan['id']);
                $scan['checks'] = $checks;
                $scan['loves'] = $loves;
                $array[] = $scan;
            }

            return $array;
        }

        return array();
    }

    static function uploadFile($oldFile, $path): bool
    {
        $relativePath = "../../";

        if ($oldFile !== null && self::isFileExists($relativePath . $oldFile))
            unlink($relativePath . $oldFile);

        if (move_uploaded_file($_FILES['file']['tmp_name'], $relativePath . $path))
            return true;

        return false;
    }
}
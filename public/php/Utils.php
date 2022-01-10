<?php

class Utils
{
    static function isValidCountry(PDO $database, $country): bool
    {
        try {
            $request = $database->prepare("SELECT * FROM jais.countries WHERE tag = :tag");
            $request->execute(array('tag' => $country));
            $rows = $request->rowCount();
            return $rows >= 1;
        } catch (Exception $exception) {
            return false;
        }
    }

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

    static function getProfile(?PDO $database, $pseudo, bool $token = false)
    {
        if ($token)
            $request = $database->prepare("SELECT id, timestamp, pseudo, token, image, role, bio FROM ziedelth.users WHERE pseudo = :pseudo");
        else
            $request = $database->prepare("SELECT id, timestamp, pseudo, image, role, bio FROM ziedelth.users WHERE pseudo = :pseudo");
        $request->execute(array('pseudo' => $pseudo));
        $user = $request->fetch(PDO::FETCH_ASSOC);

//        if ($user !== FALSE) {
//            $userId = $user['id'];
//
//            $request = $database->prepare("SELECT jais.episodes.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, episodes.episode_id, episodes.release_date, episodes.season, episodes.number, episodes.episode_type, episodes.lang_type, episodes.title, episodes.url as episode_url, episodes.image as episode_image, episodes.duration
//FROM jais.episodes INNER JOIN jais.animes a on jais.episodes.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on episodes.platform_id = p.id
//WHERE jais.episodes.id IN (SELECT episode_id FROM ziedelth.checks WHERE user_id = $userId)
//ORDER BY STR_TO_DATE(jais.episodes.release_date, '%Y-%m-%dT%TZ') DESC, a.name, jais.episodes.season DESC, jais.episodes.number DESC, jais.episodes.episode_type, jais.episodes.lang_type;");
//            $request->execute(array());
//            $user['checks'][] = $request->fetchAll(PDO::FETCH_ASSOC);
//
//            $request = $database->prepare("SELECT jais.episodes.id, c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.id as anime_id, a.name as anime, episodes.episode_id, episodes.release_date, episodes.season, episodes.number, episodes.episode_type, episodes.lang_type, episodes.title, episodes.url as episode_url, episodes.image as episode_image, episodes.duration
//FROM jais.episodes INNER JOIN jais.animes a on jais.episodes.anime_id = a.id INNER JOIN jais.countries c on a.country_id = c.id INNER JOIN jais.platforms p on episodes.platform_id = p.id
//WHERE jais.episodes.id IN (SELECT episode_id FROM ziedelth.loves WHERE user_id = $userId)
//ORDER BY STR_TO_DATE(jais.episodes.release_date, '%Y-%m-%dT%TZ') DESC, a.name, jais.episodes.season DESC, jais.episodes.number DESC, jais.episodes.episode_type, jais.episodes.lang_type;");
//            $request->execute(array());
//            $user['loves'][] = $request->fetchAll(PDO::FETCH_ASSOC);
//        }

        return $user;
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
}
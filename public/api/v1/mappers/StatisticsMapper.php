<?php

use Slim\Http\Response;

class StatisticsMapper
{
    public static function getGlobalStatistics(Response $response, PDO $pdo, int $days): Response
    {
        // If days is 0 or less, return error
        if ($days <= 0) {
            return $response->withStatus(400)->withJson(array('error' => "Days must be greater than 0"));
        }

        $dates = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));

            $request = $pdo->prepare("SELECT p.name, p.color, (SELECT COUNT(e.id) FROM jais.episodes e WHERE STR_TO_DATE(e.release_date,'%Y-%m-%d') = STR_TO_DATE('$date','%Y-%m-%dT%TZ') AND e.platform_id = p.id) as episodes, (SELECT COUNT(s.id) FROM jais.scans s WHERE STR_TO_DATE(s.release_date,'%Y-%m-%d') = STR_TO_DATE('$date','%Y-%m-%dT%TZ') AND s.platform_id = p.id) as scans FROM jais.platforms p");
            $request->execute(array());
            $platforms = $request->fetchAll(PDO::FETCH_ASSOC);

            $request = $pdo->prepare("SELECT COUNT(id) AS count FROM jais.episodes WHERE STR_TO_DATE(release_date,'%Y-%m-%d') = STR_TO_DATE('$date','%Y-%m-%dT%TZ')");
            $request->execute(array());
            $episodes = $request->fetch(PDO::FETCH_ASSOC)['count'];

            $request = $pdo->prepare("SELECT COUNT(id) AS count FROM jais.scans WHERE STR_TO_DATE(release_date,'%Y-%m-%d') = STR_TO_DATE('$date','%Y-%m-%dT%TZ')");
            $request->execute(array());
            $scans = $request->fetch(PDO::FETCH_ASSOC)['count'];

            $dates[] = array('date' => $date, 'platforms' => $platforms, 'episodes' => $episodes, 'scans' => $scans);
        }

        return $response->withJson($dates);
    }

    public static function getStatisticsForUser(PDO $pdo, $user): array
    {
        // Get all episodes notation for user
        $request = $pdo->prepare("SELECT episode_id, count FROM ziedelth.episodes_notations WHERE user_id = :user_id");
        $request->execute(array('user_id' => $user['id']));
        $episodes = $request->fetchAll(PDO::FETCH_ASSOC);

        // Get all scans for user
        $request = $pdo->prepare("SELECT scan_id, count FROM ziedelth.scans_notations WHERE user_id = :user_id");
        $request->execute(array('user_id' => $user['id']));
        $scans = $request->fetchAll(PDO::FETCH_ASSOC);

        // Get all animes for user
        $request = $pdo->prepare("SELECT anime_id, count FROM ziedelth.animes_notations WHERE user_id = :user_id");
        $request->execute(array('user_id' => $user['id']));
        $animes = $request->fetchAll(PDO::FETCH_ASSOC);

        return array('episodes' => $episodes, 'scans' => $scans, 'animes' => $animes);
    }

    public static function getUserStatistics(Response $response, PDO $pdo, string $pseudo): Response
    {
        // If pseudo do not exist, return error
        if (!MemberMapper::pseudoExists($pdo, $pseudo)) {
            return $response->withStatus(404)->withJson(array('error' => "Pseudo not found"));
        }

        // Get user
        $user = MemberMapper::getMemberWithPseudo($pdo, $pseudo);
        return $response->withJson(self::getStatisticsForUser($pdo, $user));
    }
}
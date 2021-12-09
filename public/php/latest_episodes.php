<?php

include_once 'database.php';
header('Access-Control-Allow-Origin: *');
$limit = !empty($_GET['limit']) ? intval(htmlspecialchars(strip_tags($_GET['limit']))) : 9;

try {
    $database = getPDO();
    $request = $database->query("SELECT c.name as country, p.name as platform, p.url as platform_url, p.image as platform_image, a.name as anime, episodes.episode_id, episodes.release_date, episodes.season, episodes.number, episodes.episode_type, episodes.lang_type, episodes.title, episodes.url as episode_url, episodes.image as episode_image, episodes.duration FROM episodes INNER JOIN animes a on episodes.anime_id = a.id INNER JOIN countries c on a.country_id = c.id INNER JOIN platforms p on a.platform_id = p.id ORDER BY STR_TO_DATE(episodes.release_date, '%Y-%m-%dT%TZ') DESC, a.name, episodes.season DESC, episodes.number DESC, episodes.episode_type, episodes.lang_type LIMIT $limit;");
    $request->execute();
    http_response_code(201);
    echo json_encode($request->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $exception) {
    http_response_code(500);
    echo '{"error":"' . $exception->getMessage() . '"}';
}
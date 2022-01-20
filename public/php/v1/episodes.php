<?php

require_once 'config.php';
require_once 'mappers/PlatformMapper.php';
require_once 'mappers/CountryMapper.php';
require_once 'mappers/AnimeMapper.php';
require_once 'mappers/EpisodeTypeMapper.php';
require_once 'mappers/LangTypeMapper.php';
require_once 'mappers/EpisodeMapper.php';
header('Access-Control-Allow-Origin: *');

try {
    $limit = 9;
    if (!empty($_GET['limit'])) $limit = intval(htmlspecialchars(strip_tags($_GET['limit'])));

    $pdo = getPDO();
    $episodeMapper = new EpisodeMapper();
    $episodes = $episodeMapper->getLatestEpisodes($pdo, $limit, new PlatformMapper(), new AnimeMapper(), new CountryMapper(), new EpisodeTypeMapper(), new LangTypeMapper());

    http_response_code(201);
//    var_dump($episodes);
    echo json_encode($episodes);
} catch (Exception $exception) {
    http_response_code(500);
    echo "{\"error\":\"$exception\"}";
}
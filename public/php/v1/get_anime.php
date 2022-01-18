<?php

require_once 'config.php';
require_once 'mappers/PlatformMapper.php';
require_once 'mappers/CountryMapper.php';
require_once 'mappers/AnimeGenresMapper.php';
require_once 'mappers/GenreMapper.php';
require_once 'mappers/AnimeMapper.php';
require_once 'mappers/EpisodeTypeMapper.php';
require_once 'mappers/LangTypeMapper.php';
require_once 'mappers/EpisodeMapper.php';
header('Access-Control-Allow-Origin: *');

try {
    if (!empty($_GET['id'])) {
        $id = intval(htmlspecialchars(strip_tags($_GET['id'])));
        $pdo = getPDO();
        $animeMapper = new AnimeMapper();
        $anime = $animeMapper->getAnimeById($pdo, $id, new CountryMapper(), new AnimeGenresMapper(), new GenreMapper());

        http_response_code(201);
//    var_dump($anime);
        echo json_encode($anime);
    } else {
        http_response_code(500);
        echo "{\"error\":\"Invalid format\"}";
    }
} catch (Exception $exception) {
    http_response_code(500);
    echo "{\"error\":\"$exception\"}";
}
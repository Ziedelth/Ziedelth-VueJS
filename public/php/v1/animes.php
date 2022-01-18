<?php

require_once 'config.php';
require_once 'mappers/CountryMapper.php';
require_once 'mappers/AnimeGenresMapper.php';
require_once 'mappers/GenreMapper.php';
require_once 'mappers/AnimeMapper.php';
header('Access-Control-Allow-Origin: *');

try {
    $pdo = getPDO();
    $animeMapper = new AnimeMapper();
    $animes = $animeMapper->getAllAnimes($pdo, new CountryMapper(), new AnimeGenresMapper(), new GenreMapper());

    http_response_code(201);
//    var_dump($animes);
    echo json_encode($animes);
} catch (Exception $exception) {
    http_response_code(500);
    echo "{\"error\":\"$exception\"}";
}
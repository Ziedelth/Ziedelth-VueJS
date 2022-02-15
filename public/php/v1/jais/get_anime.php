<?php

require_once "../autoload.php";
header('Access-Control-Allow-Origin: *');

try {
    if (!empty($_GET['id'])) {
        $id = intval(htmlspecialchars(strip_tags($_GET['id'])));
        $sort = empty($_GET['sort']) ? 'asc' : htmlspecialchars(strip_tags($_GET['sort']));
        $pdo = Utils::getPDO();
        $animeMapper = new AnimeMapper();
        Utils::printResponse($animeMapper->getAnimeById($pdo, $id, $sort, new CountryMapper(), new AnimeGenresMapper(), new GenreMapper()));
    } else {
        Utils::printResponse(new JSONResponse(400, array('error' => 'Bad request')));
    }
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
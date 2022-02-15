<?php

require_once "../autoload.php";
header('Access-Control-Allow-Origin: *');

try {
    $pdo = Utils::getPDO();
    $animeMapper = new AnimeMapper();
    Utils::printResponse($animeMapper->getAllAnimes($pdo, new CountryMapper()));
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
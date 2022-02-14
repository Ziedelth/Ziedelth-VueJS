<?php

require_once "autoload.php";
header('Access-Control-Allow-Origin: *');

try {
    $limit = empty($_GET['limit']) ? 9 : intval(htmlspecialchars(strip_tags($_GET['limit'])));
    $page = empty($_GET['page']) ? 1 : intval(htmlspecialchars(strip_tags($_GET['page'])));

    $pdo = getPDO();
    $episodeMapper = new EpisodeMapper();
    Utils::printResponse($episodeMapper->getLatestEpisodesPage($pdo, $limit, $page, new PlatformMapper(), new AnimeMapper(), new CountryMapper(), new EpisodeTypeMapper(), new LangTypeMapper()));
} catch (Exception $exception) {
    Utils::printResponse(new JSONResponse(500, $exception));
}
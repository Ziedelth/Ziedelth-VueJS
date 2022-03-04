<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

include "vendor/autoload.php";
include "../configurations/config.php";

$app = new App();

function getPDO(): PDO
{
    $pdo = new PDO("mysql:host=" . DATABASE_HOST . ";dbname=jais", DATABASE_USER, DATABASE_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    return $pdo;
}

/**
 * @param PDO $pdo
 * @param string $country
 * @param string $ids
 * @return array|false
 */
function getEpisodesWithIds(PDO $pdo, string $country, string $ids)
{
    if (empty($ids))
        return [];

    $request = $pdo->prepare("SELECT p.name AS platform, p.url AS platform_url, p.image AS platform_image, a.name AS anime, et.$country AS episode_type, lt.$country AS lang_type, episodes.release_date AS release_date, episodes.season AS season, episodes.number AS number, episodes.episode_id AS episode_id, episodes.title AS title, episodes.url AS url, episodes.image AS image, episodes.duration AS duration FROM episodes INNER JOIN platforms p on episodes.platform_id = p.id INNER JOIN animes a on episodes.anime_id = a.id INNER JOIN episode_types et on episodes.id_episode_type = et.id INNER JOIN lang_types lt on lt.id = episodes.id_lang_type WHERE episodes.id IN ($ids) ORDER BY episodes.release_date DESC, anime_id DESC, season DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
    $request->execute(array());
    return $request->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param PDO $pdo
 * @param string $country
 * @param string $ids
 * @return array|false
 */
function getScansWithIds(PDO $pdo, string $country, string $ids)
{
    if (empty($ids))
        return [];

    $request = $pdo->prepare("SELECT p.name AS platform, p.url AS platform_url, p.image AS platform_image, a.name AS anime, et.$country AS episode_type, lt.$country AS lang_type, scans.release_date AS release_date, scans.number AS number, scans.url AS url FROM scans INNER JOIN platforms p on scans.platform_id = p.id INNER JOIN animes a on scans.anime_id = a.id INNER JOIN episode_types et on scans.id_episode_type = et.id INNER JOIN lang_types lt on lt.id = scans.id_lang_type WHERE scans.id IN ($ids) ORDER BY scans.release_date DESC, anime_id DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
    $request->execute(array());
    return $request->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @param $array
 * @param int $page
 * @param int $limit
 * @return string
 */
function getIds($array, int $page, int $limit): string
{
    return join(", ", array_slice($array, ($page - 1) * $limit, $limit));
}

$app->get('/country/{country}/page/{page}/limit/{limit}/episodes', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $page = intval(htmlspecialchars(strip_tags($args['page'])));
        $limit = intval(htmlspecialchars(strip_tags($args['limit'])));
        $pdo = getPDO();

        $request = $pdo->prepare("SELECT episodes.id FROM episodes INNER JOIN animes a on episodes.anime_id = a.id INNER JOIN countries c on a.country_id = c.id WHERE c.tag = :country ORDER BY episodes.release_date DESC, episodes.anime_id DESC, episodes.season DESC, episodes.number DESC, episodes.id_episode_type DESC, episodes.id_lang_type DESC");
        $request->execute(array('country' => $country));
        return $response->withJson(getEpisodesWithIds($pdo, $country, getIds($request->fetchAll(PDO::FETCH_COLUMN), $page, $limit)));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong"));
    }
});

$app->get('/country/{country}/page/{page}/limit/{limit}/scans', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $page = intval(htmlspecialchars(strip_tags($args['page'])));
        $limit = intval(htmlspecialchars(strip_tags($args['limit'])));
        $pdo = getPDO();

        $request = $pdo->prepare("SELECT scans.id FROM scans INNER JOIN animes a on scans.anime_id = a.id INNER JOIN countries c on a.country_id = c.id WHERE c.tag = :country ORDER BY scans.release_date DESC, scans.anime_id DESC, scans.number DESC, scans.id_episode_type DESC, scans.id_lang_type DESC");
        $request->execute(array('country' => $country));
        return $response->withJson(getScansWithIds($pdo, $country, getIds($request->fetchAll(PDO::FETCH_COLUMN), $page, $limit)));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong"));
    }
});

$app->get('/country/{country}/animes', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $pdo = getPDO();

        $request = $pdo->prepare("SELECT animes.id AS anime_id, animes.name AS anime, animes.description AS anime_description, animes.image AS anime_image FROM animes INNER JOIN countries c on animes.country_id = c.id WHERE c.tag = :country ORDER BY animes.name");
        $request->execute(array('country' => $country));
        return $response->withJson($request->fetchAll(PDO::FETCH_ASSOC));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong"));
    }
});

try {
    $app->run();
} catch (Exception $e) {
    http_response_code(500);
    echo $e;
    die();
}
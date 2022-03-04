<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

include_once "../configurations/config.php";

include_once "./vendor/autoload.php";
include_once "./mappers/EpisodeMapper.php";
include_once "./mappers/ScanMapper.php";
include_once "./mappers/AnimeMapper.php";

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
 * @param $array
 * @param int $page
 * @param int $limit
 * @return string
 */
function ajoin($array, int $page, int $limit): string
{
    return join(", ", array_slice($array, ($page - 1) * $limit, $limit));
}

$app->get('/country/{country}/page/{page}/limit/{limit}/episodes', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $page = intval(htmlspecialchars(strip_tags($args['page'])));
        $limit = intval(htmlspecialchars(strip_tags($args['limit'])));
        $pdo = getPDO();

        $lastIds = ajoin(EpisodeMapper::getLastIds($pdo, $country), $page, $limit);
        $episodes = EpisodeMapper::getEpisodesWithIds($pdo, $country, $lastIds);
        return $response->withJson($episodes);
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception));
    }
});

$app->get('/country/{country}/page/{page}/limit/{limit}/scans', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $page = intval(htmlspecialchars(strip_tags($args['page'])));
        $limit = intval(htmlspecialchars(strip_tags($args['limit'])));
        $pdo = getPDO();

        $lastIds = ajoin(ScanMapper::getLastIds($pdo, $country), $page, $limit);
        $scans = ScanMapper::getScansWithIds($pdo, $country, $lastIds);
        return $response->withJson($scans);
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong"));
    }
});

$app->get('/country/{country}/animes', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $pdo = getPDO();
        $animes = AnimeMapper::getAllAnimes($pdo, $country);
        return $response->withJson($animes);
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong"));
    }
});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

try {
    $app->run();
} catch (Exception $e) {
    http_response_code(500);
    echo $e;
    die();
}
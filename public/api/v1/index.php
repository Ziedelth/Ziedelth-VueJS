<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

include_once "./vendor/autoload.php";
include_once "../configurations/config.php";

include_once "./templates/EmailTemplate.php";

include_once "./mappers/EpisodeMapper.php";
include_once "./mappers/ScanMapper.php";
include_once "./mappers/AnimeMapper.php";
include_once "./mappers/CountryMapper.php";

include_once "./mappers/EmailMapper.php";
include_once "./mappers/MemberMapper.php";

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/../../';

/**
 * @return PDO
 */
function getPDO(): PDO
{
    $pdo = new PDO("mysql:host=" . DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
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

$app->get('/countries', function (Request $request, Response $response) {
    try {
        $pdo = getPDO();

        $countries = CountryMapper::getAllCountries($pdo);
        return $response->withJson($countries);
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

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
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
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
         return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->get('/country/{country}/animes', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $pdo = getPDO();
        $animes = AnimeMapper::getAllAnimes($pdo, $country);
        return $response->withJson($animes);
    } catch (Exception $exception) {
         return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->get('/country/{country}/anime/{id}', function (Request $request, Response $response, $args) {
    try {
        $country = htmlspecialchars(strip_tags($args['country']));
        $id = intval(htmlspecialchars(strip_tags($args['id'])));
        $pdo = getPDO();
        $anime = AnimeMapper::getById($pdo, $country, $id);
        return $response->withJson($anime);
    } catch (Exception $exception) {
         return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/exists/pseudo', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $pseudo = htmlspecialchars(strip_tags($_['pseudo']));
        $pdo = getPDO();
        $memberExists = MemberMapper::pseudoExists($pdo, $pseudo);
        return $response->withJson(array('is_exists' => $memberExists));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/exists/email', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $email = htmlspecialchars(strip_tags($_['email']));
        $pdo = getPDO();
        $memberExists = MemberMapper::emailExists($pdo, $email);
        return $response->withJson(array('is_exists' => $memberExists));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/register', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $pseudo = htmlspecialchars(strip_tags($_['pseudo']));
        $email = htmlspecialchars(strip_tags($_['email']));
        $password = htmlspecialchars(strip_tags($_['password']));
        $pdo = getPDO();
        return $response->withJson(MemberMapper::register($pdo, $pseudo, $email, $password));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->get('/member/validate_action/{hash}', function (Request $request, Response $response, $args) {
    try {
        $hash = htmlspecialchars(strip_tags($args['hash']));
        $pdo = getPDO();
        return $response->withJson(MemberMapper::validateAction($pdo, $hash));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/login/user', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $email = htmlspecialchars(strip_tags($_['email']));
        $password = htmlspecialchars(strip_tags($_['password']));
        $pdo = getPDO();
        return $response->withJson(MemberMapper::loginWithCredentials($pdo, $email, $password));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/login/token', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $token = htmlspecialchars(strip_tags($_['token']));
        $pdo = getPDO();
        return $response->withJson(MemberMapper::loginWithToken($pdo, $token));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->get('/member/{pseudo}', function (Request $request, Response $response, $args) {
    try {
        $pseudo = htmlspecialchars(strip_tags($args['pseudo']));
        $pdo = getPDO();
        return $response->withJson(MemberMapper::getMemberWithPseudo($pdo, $pseudo));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->put('/member/update', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $token = htmlspecialchars(strip_tags($_['token']));
        $about = htmlspecialchars(strip_tags($_['about']));

        $pdo = getPDO();
        return $response->withJson(MemberMapper::update($pdo, $token, $about));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/update/image', function(Request $request, Response $response) {
    $directory = $this->get('upload_directory');
    $_ = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();

    try {
        $token = htmlspecialchars(strip_tags($_['token']));

        $pdo = getPDO();
        return $response->withJson(MemberMapper::updateImage($pdo, $token, $directory, $uploadedFiles['file']));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/delete', function(Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $token = htmlspecialchars(strip_tags($_['token']));

        $pdo = getPDO();
        return $response->withJson(MemberMapper::delete($pdo, $token));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/password_reset', function(Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $email = htmlspecialchars(strip_tags($_['email']));

        $pdo = getPDO();
        return $response->withJson(MemberMapper::passwordReset($pdo, $email));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->post('/member/confirm_password_reset', function(Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $hash = htmlspecialchars(strip_tags($_['hash']));
        $password = htmlspecialchars(strip_tags($_['password']));

        $pdo = getPDO();
        return $response->withJson(MemberMapper::confirmPasswordReset($pdo, $hash, $password));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});


$app->get('/statistics/{days}', function (Request $request, Response $response, $args) {
    try {
        $days = intval(htmlspecialchars(strip_tags($args['days']))) - 1;
        $pdo = getPDO();
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
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

$app->put('/episode/update', function (Request $request, Response $response) {
    $_ = $request->getParsedBody();

    try {
        $token = htmlspecialchars(strip_tags($_['token']));
        $id = intval(htmlspecialchars(strip_tags($_['id'])));
        $pdo = getPDO();

        if (!MemberMapper::isAdminToken($pdo, $token)) {
            return $response->withStatus(403)->withJson(array('error' => "You are not allowed to do this"));
        }

        if (!EpisodeMapper::isIDExists($pdo, $id)) {
            return $response->withStatus(404)->withJson(array('error' => "Episode not found"));
        }

        $pdo->beginTransaction();

        // Get release_date from _
        $release_date = htmlspecialchars(strip_tags($_['release_date']));

        // If release_date is not null and not empty, update it
        if (!empty($release_date)) {
            $request = $pdo->prepare("UPDATE jais.episodes SET release_date = ? WHERE id = ?");
            $request->execute(array($release_date, $id));
        }

        // Get int season from _
        $season = intval(htmlspecialchars(strip_tags($_['season'])));

        // If season is not null and not empty, update it
        if (!empty($season)) {
            $request = $pdo->prepare("UPDATE jais.episodes SET season = ? WHERE id = ?");
            $request->execute(array($season, $id));
        }

        // Get int number from _
        $number = intval(htmlspecialchars(strip_tags($_['number'])));

        // If number is not null and not empty, update it
        if (!empty($number)) {
            $request = $pdo->prepare("UPDATE jais.episodes SET number = ? WHERE id = ?");
            $request->execute(array($number, $id));
        }

        // Get title from _
        $title = htmlspecialchars(strip_tags($_['title']));

        // If title is not null and not empty, update it
        if (!empty($title)) {
            $request = $pdo->prepare("UPDATE jais.episodes SET title = ? WHERE id = ?");
            $request->execute(array($title, $id));
        }

        // Get URL from _
        $url = htmlspecialchars(strip_tags($_['url']));

        // If URL is not null and not empty, update it
        if (!empty($url)) {
            $request = $pdo->prepare("UPDATE jais.episodes SET url = ? WHERE id = ?");
            $request->execute(array($url, $id));
        }

        // Get int duration from _
        $duration = intval(htmlspecialchars(strip_tags($_['duration'])));

        // If duration is not null and not empty, update it
        if (!empty($duration)) {
            $request = $pdo->prepare("UPDATE jais.episodes SET duration = ? WHERE id = ?");
            $request->execute(array($duration, $id));
        }

        $pdo->commit();
        return $response->withJson(array('success' => "Episode updated"));
    } catch (Exception $exception) {
        return $response->withStatus(500)->withJson(array('error' => "Something went wrong", 'exception' => $exception->getMessage()));
    }
});

try {
    $app->run();
} catch (Exception|Throwable $e) {
    http_response_code(500);
    echo $e;
    die();
}
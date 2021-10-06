<?php

header("Access-Control-Allow-Origin: *");
require_once "database.php";

$country = "France";
$limit = 9;
if (isset($_GET["country"]) && !empty($_GET["country"])) $country = htmlspecialchars(strip_tags($_GET["country"]));
if (isset($_GET["limit"]) && !empty($_GET["limit"])) $limit = intval(htmlspecialchars(strip_tags($_GET["limit"])));

try {
    $pdo = getPDO();

    $request = $pdo->prepare("SELECT COUNT(*) FROM countries;");
    $request->execute(array());
    $sumCountries = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM platforms;");
    $request->execute(array());
    $sumPlatforms = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM animes;");
    $request->execute(array());
    $sumAnimes = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT COUNT(*) FROM episodes;");
    $request->execute(array());
    $sumEpisodes = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT SUM(episodes.duration) FROM episodes INNER JOIN countries ON countries.id = episodes.countryId WHERE countries.name = :country;");
    $request->execute(array("country" => $country));
    $sumDuration = intval($request->fetch()[0]);

    $request = $pdo->prepare("SELECT episodes.* FROM episodes INNER JOIN countries ON countries.id = episodes.countryId WHERE countries.name = :country ORDER BY episodes.releaseDate DESC, episodes.id ASC;");
    $request->execute(array("country" => $country));
    $array = $request->fetchAll(PDO::FETCH_ASSOC);

    $filter = [];
    $episodes = [];

    for ($i = 0; $i < count($array); $i++) {
        if (in_array($i, $filter)) continue;
        $episode = $array[$i];
        $minNumber = $episode["number"];
        $maxNumber = $episode["number"];
        $totalDuration = $episode["duration"];
        $totalEpisodes = 1;

        for ($y = $i + 1; $y < count($array); $y++) {
            if (in_array($y, $filter)) continue;
            $checkEpisode = $array[$y];

            if ($episode["platformId"] != $checkEpisode["platformId"]) continue;
            if ($episode["countryId"] != $checkEpisode["countryId"]) continue;
            if ($episode["animeId"] != $checkEpisode["animeId"]) continue;
            if ($episode["releaseDate"] != $checkEpisode["releaseDate"]) continue;
            if ($episode["season"] != $checkEpisode["season"]) continue;
            if ($episode["episodeType"] != $checkEpisode["episodeType"]) continue;
            if ($episode["langType"] != $checkEpisode["langType"]) continue;

            $minNumber = min($minNumber, $checkEpisode["number"]);
            $maxNumber = max($maxNumber, $checkEpisode["number"]);
            $totalDuration += $checkEpisode["duration"];
            $totalEpisodes++;
            array_push($filter, $y);
        }

        if ($totalEpisodes > 3) {
            unset($episode["id"]);
            unset($episode["number"]);
            unset($episode["duration"]);
            unset($episode["title"]);
            unset($episode["url"]);

            $episode["multiple"] = true;
            $episode["number"] = $minNumber . "," . $maxNumber;
            $episode["duration"] = $totalDuration / $totalEpisodes;
        } else {
            $episode["multiple"] = false;
            $filter = [];
        }

        $request = $pdo->prepare("SELECT * FROM platforms WHERE id = :id;");
        $request->execute(array("id" => intval($episode["platformId"])));
        $episode["platform"] = $request->fetchAll(PDO::FETCH_ASSOC)[0];
        unset($episode["platformId"]);

        $request = $pdo->prepare("SELECT * FROM countries WHERE id = :id;");
        $request->execute(array("id" => intval($episode["countryId"])));
        $episode["country"] = $request->fetchAll(PDO::FETCH_ASSOC)[0];
        unset($episode["countryId"]);

        $request = $pdo->prepare("SELECT * FROM animes WHERE id = :id;");
        $request->execute(array("id" => intval($episode["animeId"])));
        $episode["anime"] = $request->fetchAll(PDO::FETCH_ASSOC)[0];
        unset($episode["animeId"]);

        ksort($episode);

        array_push($episodes, $episode);
        if (count($episodes) >= $limit) break;
    }

    echo json_encode(array(
        "countries" => $sumCountries,
        "platforms" => $sumPlatforms,
        "animes" => $sumAnimes,
        "episodes" => $sumEpisodes,
        "duration" => $sumDuration,
        "latest_episodes" => $episodes
    ));
} catch (PDOException $ex) {
    echo json_encode(array("error" => $ex));
}


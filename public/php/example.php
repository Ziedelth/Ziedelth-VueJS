<?php

header('Access-Control-Allow-Origin: *');

$limit = 9;
if (isset($_GET['limit']) && !empty($_GET['limit'])) $limit = htmlspecialchars(strip_tags($_GET['limit']));

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "ziedelth";
$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$episodes = [];

try {
    $request = $pdo->prepare("SELECT platforms.name AS platformName, platforms.url AS platformUrl, platforms.image AS platformImage, countries.season AS countrySeason, countries.episode AS countryEpisode, countries.film AS countryFilm, countries.subtitles AS countrySubtitles, countries.dubbed AS countryDubbed, animes.name AS animeName, animes.image AS animeImage, episodes.*
FROM episodes
    INNER JOIN platforms ON platforms.id = episodes.platformId
    INNER JOIN countries ON countries.id = episodes.countryId
    INNER JOIN animes ON animes.id = episodes.animeId
WHERE countries.name = :country
ORDER BY episodes.releaseDate DESC, episodes.id DESC;");
    $request->execute(array('country' => 'France'));
    $array = $request->fetchAll(PDO::FETCH_ASSOC);
    $filter = [];

    for ($i = 0; $i < count($array); $i++) {
        $episode = $array[$i];
        $minNumber = $episode["number"];
        $maxNumber = $episode["number"];
        $totalDuration = $episode["duration"];
        $totalEpisodes = 1;

        for ($y = $i + 1; $y < count($array); $y++) {
            if (in_array($y, $filter)) continue;
            $checkEpisode = $array[$y];

            if ($episode["animeName"] != $checkEpisode["animeName"]) continue;
            if ($episode["releaseDate"] != $checkEpisode["releaseDate"]) continue;
            if ($episode["season"] != $checkEpisode["season"]) continue;
            if ($episode["episodeType"] != $checkEpisode["episodeType"]) continue;
            if ($episode["langType"] != $checkEpisode["langType"]) continue;

            $minNumber = min($minNumber, $checkEpisode['number']);
            $maxNumber = max($maxNumber, $checkEpisode['number']);
            $totalDuration += $checkEpisode["duration"];
            $totalEpisodes++;
            array_push($filter, $y);
        }

        if ($minNumber != $maxNumber && abs($maxNumber - $minNumber) > 2) {
            unset($episode["number"]);
            unset($episode["duration"]);
            unset($episode["title"]);
            unset($episode["url"]);

            $episode["minNumber"] = $minNumber;
            $episode["maxNumber"] = $maxNumber;
            $episode["durationAVG"] = $totalDuration / $totalEpisodes;
        }

        array_push($episodes, $episode);
        if (count($episodes) >= $limit) break;
    }
} catch (PDOException $ex) {
}

echo json_encode($episodes);
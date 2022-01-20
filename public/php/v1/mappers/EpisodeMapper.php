<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class Episode extends JObject
{
    public ?Platform $platform;
    public ?AnimeEpisode $anime;
    public ?EpisodeType $episodeType;
    public ?LangType $langType;
    public string $releaseDate;
    public int $season;
    public int $number;
    public string $episodeId;
    public ?string $title;
    public string $url;
    public string $image;
    public int $duration;
    private int $id;
    private int $platformId;
    private int $animeId;
    private int $idEpisodeType;
    private int $idLangType;

    public function __construct(?PDO $pdo = null, ?PlatformMapper $platformMapper = null, ?AnimeMapper $animeMapper = null, ?CountryMapper $countryMapper = null, ?EpisodeTypeMapper $episodeTypeMapper = null, ?LangTypeMapper $langTypeMapper = null)
    {
        if ($pdo != null && $platformMapper != null && $animeMapper != null && $episodeTypeMapper != null && $langTypeMapper != null) {
            $this->platform = $platformMapper->getPlatformById($pdo, $this->platformId);
            $this->anime = $animeMapper->getAnimeEpisodeById($pdo, $this->animeId, $countryMapper);
            $this->episodeType = $episodeTypeMapper->getEpisodeTypeById($pdo, $this->idEpisodeType);
            $this->langType = $langTypeMapper->getLangTypeById($pdo, $this->idLangType);
        }
    }
}

class EpisodeMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.episodes', 'Episode');
    }

    function getAllEpisodes(?PDO $pdo): array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className);
    }

    function getEpisodesBy(?PDO $pdo, int $animeId, $season, $idEpisodeType, $number, $idLangType, PlatformMapper $platformMapper, AnimeMapper $animeMapper, CountryMapper $countryMapper, EpisodeTypeMapper $episodeTypeMapper, LangTypeMapper $langTypeMapper): ?array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE anime_id = :animeId AND season = :season AND id_episode_type = :idEpisodeType AND number = :number AND id_lang_type = :idLangType");
        $request->execute(array('animeId' => $animeId, 'season' => $season, 'idEpisodeType' => $idEpisodeType, 'number' => $number, 'idLangType' => $idLangType));
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className, [$pdo, $platformMapper, $animeMapper, $countryMapper, $episodeTypeMapper, $langTypeMapper]);
    }

    function getLatestEpisodesPage(?PDO $pdo, int $limit, int $page, PlatformMapper $platformMapper, AnimeMapper $animeMapper, CountryMapper $countryMapper, EpisodeTypeMapper $episodeTypeMapper, LangTypeMapper $langTypeMapper): array
    {
        $request = $pdo->prepare("SELECT id FROM $this->tableName ORDER BY release_date DESC, anime_id DESC, season DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
        $request->execute(array());
        $array = $request->fetchAll(PDO::FETCH_COLUMN);
        $ids = join(', ', array_slice($array, ($page - 1) * $limit, $limit));

        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id IN ($ids) ORDER BY release_date DESC, anime_id DESC, season DESC, number DESC, id_episode_type DESC, id_lang_type DESC");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className, [$pdo, $platformMapper, $animeMapper, $countryMapper, $episodeTypeMapper, $langTypeMapper]);
    }

    function getLatestEpisodes(?PDO $pdo, int $limit, PlatformMapper $platformMapper, AnimeMapper $animeMapper, CountryMapper $countryMapper, EpisodeTypeMapper $episodeTypeMapper, LangTypeMapper $langTypeMapper): array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName ORDER BY release_date DESC, anime_id DESC, season DESC, number DESC, id_episode_type DESC, id_lang_type DESC LIMIT $limit");
        $request->execute(array());
        return $request->fetchAll(PDO::FETCH_CLASS, $this->className, [$pdo, $platformMapper, $animeMapper, $countryMapper, $episodeTypeMapper, $langTypeMapper]);
    }
}
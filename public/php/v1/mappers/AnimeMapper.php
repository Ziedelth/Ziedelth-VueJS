<?php

require_once 'JObject.php';
require_once 'Mapper.php';

class AnimeEpisode extends JObject
{
    public int $id;
    public ?Country $country;
    public string $name;
    public ?string $image;
    public ?string $description;
    protected int $countryId;
    protected string $releaseDate;
    protected string $code;

    public function __construct(?PDO $pdo = null, ?CountryMapper $countryMapper = null)
    {
        if ($pdo != null && $countryMapper != null) {
            $this->country = $countryMapper->getCountryById($pdo, $this->countryId);
        }
    }
}

class Anime extends AnimeEpisode
{
    public array $seasons = [];
    public ?array $genres;

    public function __construct(?PDO $pdo = null, ?CountryMapper $countryMapper = null, ?AnimeGenresMapper $animeGenresMapper = null, ?GenreMapper $genreMapper = null)
    {
        parent::__construct($pdo, $countryMapper);

        if ($pdo != null && $countryMapper != null && $animeGenresMapper != null && $genreMapper != null) {
            $episodeMapper = new EpisodeMapper();

            $request = $pdo->prepare("SELECT DISTINCT season FROM " . $episodeMapper->tableName . " WHERE anime_id = :anime_id");
            $request->execute(array('anime_id' => $this->id));

            foreach ($request->fetchAll(PDO::FETCH_COLUMN) as $season) {
                $request = $pdo->prepare("SELECT DISTINCT CONCAT(et.fr, ' ', number, ' ', lt.fr) AS resume, id_episode_type, number, id_lang_type FROM " . $episodeMapper->tableName . " JOIN jais.episode_types et ON et.id = id_episode_type JOIN jais.lang_types lt ON lt.id = id_lang_type WHERE anime_id = :anime_id AND season = :season ORDER BY id_episode_type, number, id_lang_type");
                $request->execute(array('anime_id' => $this->id, 'season' => $season));
                $array = [];
                $array['season'] = $season;

                foreach ($request->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    $object = $item;
                    $object['list'] = $episodeMapper->getEpisodesBy($pdo, $this->id, $season, $item['id_episode_type'], $item['number'], $item['id_lang_type'], new PlatformMapper(), new AnimeMapper(), $countryMapper, new EpisodeTypeMapper(), new LangTypeMapper());
                    $array['episodes'][] = $object;
                }

                $this->seasons[] = $array;
            }

            $animeGenres = $animeGenresMapper->getGenresByAnimeId($pdo, $this->id);

            if (!empty($animeGenres))
                $this->genres = $genreMapper->getGenresByIds($pdo, join(', ', $animeGenres));
            else
                $this->genres = array();
        }
    }
}

class AnimeMapper extends Mapper
{
    public function __construct()
    {
        parent::__construct('jais.animes');
    }

    function getAllAnimes(?PDO $pdo, ?CountryMapper $countryMapper = null): array
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName");
        $request->execute(array());
        $array = $request->fetchAll(PDO::FETCH_CLASS, 'AnimeEpisode', [$pdo, $countryMapper]);
        usort($array, function (AnimeEpisode $a, AnimeEpisode $b) {
            return strtolower($a->name) <=> strtolower($b->name);
        });
        return $array;
    }

    function getAnimeEpisodeById(?PDO $pdo, $id, ?CountryMapper $countryMapper = null): ?AnimeEpisode
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchObject('AnimeEpisode', [$pdo, $countryMapper]);
    }

    function getAnimeById(?PDO $pdo, $id, ?CountryMapper $countryMapper = null, ?AnimeGenresMapper $animeGenresMapper = null, ?GenreMapper $genreMapper = null): ?Anime
    {
        $request = $pdo->prepare("SELECT * FROM $this->tableName WHERE id = :id");
        $request->execute(array('id' => $id));
        return $request->fetchObject('Anime', [$pdo, $countryMapper, $animeGenresMapper, $genreMapper]);
    }
}
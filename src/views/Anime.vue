<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else class="container-fluid">
        <div class="container">
          <img :src="anime.image" alt="Anime image" class="img-fluid rounded mb-3">
          <h3>{{ anime.name }}</h3>
          <p>{{ anime.genres.map((e) => e.fr).join(' - ') }}</p>
          <hr>
          <p>Temps total : {{ getTotalTime() }}</p>
          <p class="text-muted">{{ getAnimeDescription() }}</p>
        </div>

        <select v-if="anime.seasons.length > 0" v-model="selectedSeason" class="form-select-sm px-3 mb-3">
          <option v-for="season in anime.seasons" :value="season.season">{{ anime.country.season }} {{
              season.season
            }}
          </option>
        </select>

        <div class="row g-3">
          <div v-for="episode in getSelectedSeason().episodes" :key="episode.episodeId" class="col-lg-4">
            <EpisodeComponent :episode="episode"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

const EpisodeComponent = () => import("@/components/EpisodeComponent");
const LoadingComponent = () => import("@/components/LoadingComponent");

export default {
  components: {EpisodeComponent, LoadingComponent},
  data() {
    return {
      id: this.$route.params.id,
      isLoading: true,
      error: null,
      anime: [],
      selectedSeason: null,
    }
  },
  methods: {
    getAnimeDescription() {
      return (this.anime.description === null || this.anime.description.length <= 0) ? 'Aucune description pour le moment...' : this.anime.description;
    },
    getSelectedSeason() {
      for (const season of this.anime.seasons) {
        if (season.season === this.selectedSeason) {
          return season;
        }
      }

      return null;
    },
    getTotalTime() {
      let duration = 0;

      for (const season of this.anime.seasons) {
        for (const episode of season.episodes) {
          duration += episode.duration;
        }
      }

      return Utils.getTimeFormat(duration);
    }
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/v1/get_anime.php?id=" + this.id))

      if (response.status === 201) {
        this.anime = await response.json();
        this.selectedSeason = this.anime.seasons[0].season;
        console.log(this.selectedSeason)
        this.error = null;
      } else {
        this.anime = [];
        this.error = response.statusText;
      }
    } catch (exception) {
      this.anime = [];
      this.error = exception;
    }

    this.isLoading = false;
  }
}
</script>
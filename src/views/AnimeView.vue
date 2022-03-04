<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else class="container-fluid">
        <div class="container">
          <img :src="anime.image" alt="Anime image" class="w-10 rounded mb-3">
          <h3>{{ anime.name }}</h3>
          <p>{{ anime.genres.map((e) => e.fr).join(' - ') }}</p>
          <hr>
          <p v-if="anime.seasons.length > 0">Temps total : {{ getTotalTime() }}</p>
          <p class="text-muted">{{ getAnimeDescription() }}</p>
        </div>

        <div class="d-inline-flex mb-3">
          <div class="form-check me-3">
            <input id="flexRadioDefault1" v-model="showType" class="form-check-input" name="flexRadioDefault"
                   type="radio"
                   value="episodes">
            <label class="form-check-label" for="flexRadioDefault1">Épisodes</label>
          </div>
          <div class="form-check">
            <input id="flexRadioDefault2" v-model="showType" class="form-check-input" name="flexRadioDefault"
                   type="radio"
                   value="scans">
            <label class="form-check-label" for="flexRadioDefault2">Scans</label>
          </div>
        </div>

        <div class="mb-3">
          <button :class="{'active': sort === 'asc'}" class="btn btn-outline-secondary mx-1"
                  @click="sort = 'asc'; update()"><i class="bi bi-sort-numeric-down"></i></button>
          <button :class="{'active': sort === 'desc'}" class="btn btn-outline-secondary mx-1"
                  @click="sort = 'desc'; update()"><i class="bi bi-sort-numeric-up"></i></button>
        </div>

        <div v-if="anime.seasons.length > 0 && showType === 'episodes'">
          <select v-model="selectedSeason" class="form-select-sm px-3 mb-3">
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
        <div v-if="anime.scans.length > 0 && showType === 'scans'">
          <div class="row g-3">
            <div v-for="scan in anime.scans" :key="scan.id" class="col-lg-4">
              <ScanComponent :scan="scan"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

const EpisodeComponent = () => import("@/components/EpisodeComponent");
const ScanComponent = () => import("@/components/ScanComponent");
const LoadingComponent = () => import("@/components/LoadingComponent");

export default {
  components: {ScanComponent, EpisodeComponent, LoadingComponent},
  data() {
    return {
      sort: 'asc',
      showType: 'episodes',
      isLoading: true,
      error: null,
      anime: [],
      selectedSeason: null,
    }
  },
  methods: {
    async update() {
      this.isLoading = true

      await Utils.get(`php/v1/jais/get_anime.php?id=${this.$route.params.id}&sort=${this.sort}`, 200, (anime) => {
        this.anime = anime
        const a = anime.seasons.length > 0
        this.selectedSeason = a ? anime.seasons[0].season : null
        this.showType = a ? 'episodes' : 'scans'
      }, (failed) => {
        this.error = `${failed}`
      })

      this.isLoading = false
    },
    getAnimeDescription() {
      return Utils.isNullOrEmpty(this.anime.description) ? 'Aucune description pour le moment...' : this.anime.description
    },
    getSelectedSeason() {
      for (const season of this.anime.seasons)
        if (season.season === this.selectedSeason)
          return season

      return null
    },
    getTotalTime() {
      let duration = 0

      for (const season of this.anime.seasons)
        for (const episode of season.episodes)
          duration += episode.duration

      return Utils.getTimeFormat(duration)
    }
  },
  async mounted() {
    await this.update()
  }
}
</script>

<style scoped>
.w-10 {
  width: 10% !important
}
</style>
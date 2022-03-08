<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else class="container-fluid">
        <div class="container">
          <img :src="anime.image" alt="Anime image" class="w-10 rounded mb-3">
          <h3>{{ anime.name }}</h3>
          <p>{{ anime.genres }}</p>
          <hr>
          <p v-if="anime.seasons.length > 0">Temps total : {{ getTotalTime() }}</p>
          <p class="text-muted">{{ getAnimeDescription() }}</p>
        </div>

        <div class="d-inline-flex mb-3">
          <div v-if="anime.seasons.length > 0" class="form-check me-3">
            <input id="flexRadioDefault1" v-model="showType" class="form-check-input" name="flexRadioDefault" type="radio" value="episodes">
            <label class="form-check-label" for="flexRadioDefault1">Épisodes</label>
          </div>
          <div v-if="anime.scans.length > 0" class="form-check">
            <input id="flexRadioDefault2" v-model="showType" class="form-check-input" name="flexRadioDefault" type="radio" value="scans">
            <label class="form-check-label" for="flexRadioDefault2">Scans</label>
          </div>
        </div>

        <div class="mb-3">
          <b-icon-funnel-fill class="me-2" scale="1.5" />
          <button :class="{'active': sort === 'desc'}" class="btn btn-outline-secondary mx-1" @click="sort = 'desc'"><b-icon-sort-numeric-up /></button>
          <button :class="{'active': sort === 'asc'}" class="btn btn-outline-secondary mx-1" @click="sort = 'asc'"><b-icon-sort-numeric-down /></button>
        </div>

        <div v-if="anime.seasons.length > 0 && showType === 'episodes'">
          <select v-model="selectedSeason" class="form-select-sm px-3 mb-3">
            <option v-for="season in anime.seasons" :value="season.season">{{ anime.country_season }} {{ season.season }}</option>
          </select>

          <div class="row g-3">
            <div v-for="episode in episodes" :key="episode.episodeId" class="col-lg-4">
              <EpisodeComponent :episode="episode"/>
            </div>
          </div>
        </div>
        <div v-if="anime.scans.length > 0 && showType === 'scans'">
          <div class="row g-3">
            <div v-for="scan in scans" :key="scan.id" class="col-lg-4">
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
import {mapState} from "vuex";

const EpisodeComponent = () => import("@/components/EpisodeComponent");
const ScanComponent = () => import("@/components/ScanComponent");
const LoadingComponent = () => import("@/components/LoadingComponent");

export default {
  components: {ScanComponent, EpisodeComponent, LoadingComponent},
  computed: {
    ...mapState(['currentCountry']),

    episodes() {
      return this.getSelectedSeason().episodes.sort((a, b) => this.sort === 'asc' ? (a.season << a.number) - (b.season << b.number) : (b.season << b.number) - (a.season << a.number))
    },
    scans() {
      return this.anime.scans.sort((a, b) => this.sort === 'asc' ? a.number - b.number : b.number - a.number)
    }
  },
  data() {
    return {
      sort: 'desc',
      showType: 'episodes',

      error: null,
      isLoading: true,
      anime: [],
      selectedSeason: null,
    }
  },
  methods: {
    async update() {
      this.isLoading = true

      await Utils.get(`api/v1/country/${this.currentCountry.tag}/anime/${this.$route.params.id}`, (success) => {
        this.anime = success
        this.selectedSeason = success.seasons.length > 0 ? success.seasons[0].season : null
        this.showType = success.seasons.length > 0 ? 'episodes' : 'scans'
      }, (failed) => {
        this.error = failed
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
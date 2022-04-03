<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else class="container-fluid">
        <div class="container">
          <figure v-lazyload class="m-0 p-0">
            <img :data-url="anime.image" alt="Anime image" class="w-10 rounded mb-3">
          </figure>

          <h3>{{ anime.name }}</h3>
          <p>{{ anime.genres }}</p>
          <hr>
          <p class="text-muted">{{ getAnimeDescription() }}</p>
        </div>

        <div class="d-inline-flex mb-3">
          <div v-if="anime.seasons.length > 0 && anime.scans.length > 0" class="form-check me-3">
            <input id="flexRadioDefault1" v-model="showType" class="form-check-input" name="flexRadioDefault" type="radio" value="episodes">
            <label class="form-check-label" for="flexRadioDefault1">Épisodes</label>
          </div>
          <div v-if="anime.seasons.length > 0 && anime.scans.length > 0" class="form-check">
            <input id="flexRadioDefault2" v-model="showType" class="form-check-input" name="flexRadioDefault" type="radio" value="scans">
            <label class="form-check-label" for="flexRadioDefault2">Scans</label>
          </div>
        </div>

        <div class="mb-3">
          <b-icon-funnel-fill class="me-2" scale="1.5" />
          <button :class="{'active': filter === 'desc_number'}" class="btn btn-outline-secondary mx-1" @click="filter = 'desc_number'"><b-icon-sort-numeric-up /></button>
          <button :class="{'active': filter === 'asc_number'}" class="btn btn-outline-secondary mx-1" @click="filter = 'asc_number'"><b-icon-sort-numeric-down /></button>
          <button :class="{'active': filter === 'asc_time'}" class="btn btn-outline-secondary mx-1" @click="filter = 'asc_time'"><b-icon-calendar-plus /></button>
          <button :class="{'active': filter === 'desc_time'}" class="btn btn-outline-secondary mx-1" @click="filter = 'desc_time'"><b-icon-calendar-minus /></button>
        </div>

        <div v-if="anime.seasons.length > 0 && showType === 'episodes'">
          <select v-if="anime.seasons.length > 1" v-model="selectedSeason" class="form-select-sm px-3 mb-3">
            <option v-for="season in anime.seasons" :value="season.season">{{ anime.country_season }} {{ season.season }}</option>
          </select>

          <Episodes :episodes="episodes" @refresh="update"/>
        </div>
        <div v-if="anime.scans.length > 0 && showType === 'scans'">
          <Scans :scans="scans" @refresh="update" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapState} from "vuex";

const LoadingComponent = () => import("@/components/LoadingComponent");
const Episodes = () => import("@/components/Episodes");
const Scans = () => import("@/components/Scans");

export default {
  components: {LoadingComponent, Episodes, Scans},
  computed: {
    ...mapState(['currentCountry']),

    episodes() {
      return this.getSelectedSeason().episodes.sort((a, b) => {
        switch (this.filter) {
          case "asc_number":
            return a.number - b.number
          case "desc_number":
            return b.number - a.number
          case "desc_time":
            return new Date(a.release_date).getTime() - new Date(b.release_date).getTime()
          case "asc_time":
            return new Date(b.release_date).getTime() - new Date(a.release_date).getTime()
        }
      })
    },
    scans() {
      return this.anime.scans.sort((a, b) => {
        switch (this.filter) {
          case "asc_number":
            return a.number - b.number
          case "desc_number":
            return b.number - a.number
          case "desc_time":
            return new Date(a.release_date).getTime() - new Date(b.release_date).getTime()
          case "asc_time":
            return new Date(b.release_date).getTime() - new Date(a.release_date).getTime()
        }
      })
    }
  },
  data() {
    return {
      filter: 'desc_number',
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
<template>
  <div id="jais" class="text-center">
    <img alt="Jaïs brand" class="img-fluid collapsed border-color rounded-circle shadow" height="120"
         loading="lazy" src="images/jais.jpg" width="120"/>

    <div class="container-fluid">
      <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
      <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>

      <div class="d-inline w-100">
        <a class="link-color" href="https://twitter.com/Jaiss___" target="_blank"><b-icon-twitter class="mx-2" /></a>
        <a class="link-color" href="https://www.instagram.com/jais_zie/" target="_blank"><b-icon-instagram class="mx-2" /></a>
        <a class="link-color" href="https://github.com/Ziedelth/Jais" target="_blank"><b-icon-github class="mx-2" /></a>
        <a class="link-color" href="#"><b-icon-discord class="mx-2" /></a>
      </div>
    </div>

    <LoadingComponent :is-loading="isLoading" />

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else>
        <div class="mb-3">
          <p class="text mt-0 muted fw-bold">Les derniers épisodes sortis</p>

          <div class="row g-3">
            <div v-for="episode in episodes" class="col-lg-3">
              <EpisodeComponent :episode="episode"/>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <p class="text mt-0 muted fw-bold">Les derniers scans sortis</p>

          <div class="row g-3">
            <div v-for="scan in scans" class="col-lg-3">
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

const LoadingComponent = () => import("@/components/LoadingComponent");
const EpisodeComponent = () => import("@/components/EpisodeComponent");
const ScanComponent = () => import("@/components/ScanComponent");

export default {
  name: "Jais",
  components: {LoadingComponent, EpisodeComponent, ScanComponent},
  computed: {
    ...mapState(['currentCountry'])
  },
  data() {
    return {
      limit: 12,
      pageEpisodes: 1,
      pageScans: 1,

      isLoading: true,
      episodes: [],
      scans: [],
      error: null,
    }
  },
  async mounted() {
    this.isLoading = true

    await Utils.get(`api/v1/country/${this.currentCountry.tag}/page/${this.pageEpisodes}/limit/${this.limit}/episodes`, (success) => {
      this.episodes.push(...success)
    }, (failed) => {
      this.error = failed
    })

    await Utils.get(`api/v1/country/${this.currentCountry.tag}/page/${this.pageScans}/limit/${this.limit}/scans`, (success) => {
      this.scans.push(...success)
    }, (failed) => {
      this.error = failed
    })

    this.isLoading = false
  }
}
</script>


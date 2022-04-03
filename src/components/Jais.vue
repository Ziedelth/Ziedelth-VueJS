<template>
  <div id="jais" class="text-center">
    <figure v-lazyload class="m-0 p-0">
      <img alt="Jaïs brand" class="img-fluid collapsed border-color rounded-circle shadow" data-url="images/jais.jpg" width="120" height="120"/>
    </figure>

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
          <Episodes :episodes="episodes" @refresh="getEpisodes"/>
        </div>

        <div class="mb-3">
          <p class="text mt-0 muted fw-bold">Les derniers scans sortis</p>
          <Scans :scans="scans" @refresh="getScans"/>
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
  name: "Jais",
  components: {LoadingComponent, Episodes, Scans},
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
  methods: {
    // Get episodes
    async getEpisodes() {
      await Utils.get(`api/v1/country/${this.currentCountry.tag}/page/${this.pageEpisodes}/limit/${this.limit}/episodes`, (success) => {
        this.episodes = success
      }, (failed) => {
        this.error = failed
      })
    },

    // Get scans
    async getScans() {
      await Utils.get(`api/v1/country/${this.currentCountry.tag}/page/${this.pageScans}/limit/${this.limit}/scans`, (success) => {
        this.scans = success
      }, (failed) => {
        this.error = failed
      })
    },
  },
  async mounted() {
    this.isLoading = true
    await this.getEpisodes()
    await this.getScans()
    this.isLoading = false
  }
}
</script>


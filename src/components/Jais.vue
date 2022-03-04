<template>
  <div id="jais" class="text-center">
    <img alt="Jaïs brand" class="img-fluid collapsed border-color rounded-circle shadow" height="120"
         loading="lazy" src="images/jais.jpg" width="120"/>

    <div class="container-fluid">
      <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
      <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>

      <div class="d-inline w-100">
        <a class="link-color" href="https://twitter.com/Jaiss___" target="_blank"><i class="bi bi-twitter mx-2"></i></a>
        <a class="link-color" href="https://www.instagram.com/jais_zie/" target="_blank"><i
            class="bi bi-instagram mx-2"></i></a>
        <a class="link-color" href="https://github.com/Ziedelth/Jais" target="_blank"><i class="bi bi-github mx-2"></i></a>
        <a class="link-color" href="#"><i class="bi bi-discord mx-2"></i></a>
      </div>

      <p class="text mt-0 muted fw-bold">Les dernières sorties</p>
    </div>

    <LoadingComponent :is-loading="isLoading" />

    <div v-if="!isLoading">
      <div class="d-inline-flex mb-3">
        <div class="form-check me-3">
          <input id="flexRadioDefault1" v-model="showType" class="form-check-input" name="flexRadioDefault" type="radio"
                 value="episodes">
          <label class="form-check-label" for="flexRadioDefault1">Épisodes</label>
        </div>
        <div class="form-check">
          <input id="flexRadioDefault2" v-model="showType" class="form-check-input" name="flexRadioDefault" type="radio"
                 value="scans">
          <label class="form-check-label" for="flexRadioDefault2">Scans</label>
        </div>
      </div>

      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else>
        <div v-if="showType === 'episodes'" id="episodes">
          <div class="row g-3">
            <div v-for="episode in episodes" class="col-lg-4">
              <EpisodeComponent :episode="episode"/>
            </div>
          </div>
        </div>

        <div v-if="showType === 'scans'" id="scans">
          <div class="row g-3">
            <div v-for="scan in scans" class="col-lg-4">
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
      showType: 'episodes',

      limit: 9,
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

    await Utils.get(`api/v1/country/${this.currentCountry.tag}/page/${this.pageEpisodes}/limit/${this.limit}/episodes`, 200, (success) => {
      this.episodes.push(...success)
    }, (failed) => {
      this.error = failed
    })

    await Utils.get(`api/v1/country/${this.currentCountry.tag}/page/${this.pageScans}/limit/${this.limit}/scans`, 200, (success) => {
      this.scans.push(...success)
    }, (failed) => {
      this.error = failed
    })

    this.isLoading = false
  }
}
</script>


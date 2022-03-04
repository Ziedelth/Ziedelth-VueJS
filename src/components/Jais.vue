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

    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading" class="container-fluid">
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

      <div v-else class="row g-3">
        <div v-for="episode in episodes" v-if="showType === 'episodes'" class="col-lg-4">
          <EpisodeComponent :episode="episode"/>
        </div>

        <div v-for="scan in scans" v-if="showType === 'scans'" class="col-lg-4">
          <ScanComponent :scan="scan"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

const LoadingComponent = () => import("@/components/LoadingComponent");
const EpisodeComponent = () => import("@/components/EpisodeComponent");
const ScanComponent = () => import("@/components/ScanComponent");

export default {
  name: "Jais",
  components: {LoadingComponent, EpisodeComponent, ScanComponent},
  data() {
    return {
      showType: 'episodes',
      isLoading: true,
      limit: 9,
      pageEpisodes: 1,
      pageScans: 1,
      error: null,
      episodes: [],
      scans: [],
    }
  },
  methods: {
    async getEpisodes() {
      try {
        const response = await fetch(Utils.getLocalFile(`api/v1/country/fr/page/${this.pageEpisodes}/limit/${this.limit}/episodes`))

        if (response.status !== 200) {
          this.episodes = []
          this.error = response.statusText
          return
        }

        this.episodes.push(...(await response.json()))
        this.error = null
      } catch (exception) {
        this.episodes = []
        this.error = exception
      }
    },
    async getScans() {
      try {
        const response = await fetch(Utils.getLocalFile(`api/v1/country/fr/page/${this.pageScans}/limit/${this.limit}/scans`))

        if (response.status !== 200) {
          this.scans = []
          this.error = response.statusText
          return
        }

        this.scans.push(...(await response.json()))
        this.error = null
      } catch (exception) {
        this.scans = []
        this.error = exception
      }
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


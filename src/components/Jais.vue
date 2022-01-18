<template>
  <div id="jais" class="text-center">
    <img alt="Jaïs brand" class="img-fluid collapsed border-color rounded-circle shadow" height="120"
         loading="lazy" src="images/jais.jpg" width="120"/>

    <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
    <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>
    <p class="text mt-0 muted fw-bold">Les dernières sorties</p>

    <div v-if="isLoading" class="spinner-border my-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>

    <div v-else class="container-fluid">
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

const EpisodeComponent = () => import("@/components/EpisodeComponent");
const ScanComponent = () => import("@/components/ScanComponent");

export default {
  name: "Jais",
  components: {EpisodeComponent, ScanComponent},
  data() {
    return {
      showType: 'episodes',
      isLoading: true,
      limitEpisodes: 9,
      limitScans: 9,
      error: null,
      episodes: [],
      scans: [],
    }
  },
  methods: {
    async getEpisodes(add = false) {
      if (add)
        this.limitEpisodes += 9;

      try {
        const response = await fetch(Utils.getLocalFile("php/v1/episodes.php?limit=" + this.limitEpisodes))

        if (response.status === 201) {
          this.episodes = await response.json();
          this.error = null;
        } else {
          this.episodes = [];
          this.error = response.statusText;
        }
      } catch (exception) {
        this.episodes = [];
        this.error = exception;
      }
    },
    async getScans(add = false) {
      if (add)
        this.limitScans += 9;

      try {
        const response = await fetch(Utils.getLocalFile("php/v1/scans.php?limit=" + this.limitScans))

        if (response.status === 201) {
          this.scans = await response.json();
          this.error = null;
        } else {
          this.scans = [];
          this.error = response.statusText;
        }
      } catch (exception) {
        this.scans = [];
        this.error = exception;
      }
    },
  },
  async mounted() {
    this.isLoading = true
    await this.getEpisodes()
    await this.getScans()
    this.isLoading = false

    setInterval(async () => {
      if (this.showType === 'episodes')
        await this.getEpisodes()
      else if (this.showType === 'scans')
        await this.getScans()
    }, 60 * 1000);

    window.onscroll = async () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

      if (bottomOfWindow && !this.isLoading) {
        if (this.showType === 'episodes')
          await this.getEpisodes(true)
        else if (this.showType === 'scans')
          await this.getScans(true)
      }
    }
  }
}
</script>


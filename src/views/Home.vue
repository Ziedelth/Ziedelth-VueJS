<template>
  <div>
    <div id="jais" class="text-center">
      <img :src="getJaisImage" alt="Ziedelth.fr brand" class="rounded-circle border shadow" style="width: 7.5rem">
      <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
      <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>
      <p class="text mt-0 muted fw-bold">Les dernières sorties</p>

      <div v-show="isEmpty" class="spinner-border">
        <span class="visually-hidden">Loading...</span>
      </div>

      <b-container v-show="!isEmpty" fluid>
        <b-row class="g-3">
          <NewEpisode v-for="episode in getLatestEpisodes" :key="episode.eId" :episode="episode"/>
        </b-row>
      </b-container>

      <b-container class="mt-3" fluid>
        <b-btn variant="light" @click="fetchEpisodes(9)">Voir plus</b-btn>

        <p class="p-3 text-muted">Vous souhaitez voir les actualités à propos des animés et mangas ?
          <router-link class="text-decoration-none" to="/">Venez ici</router-link>
        </p>
      </b-container>
    </div>
  </div>
</template>

<script>
import {mapActions, mapGetters, mapState} from "vuex";
import Utils from "@/utils";
import NewEpisode from "@/components/Episode";

export default {
  components: {NewEpisode},
  computed: {
    ...mapState(["limit", "fullData"]),
    ...mapGetters(["isEmpty", "getJaisImage"]),

    getLatestEpisodes() {
      return Utils.getInData(this.fullData, "latest_episodes", [])
    }
  },
  data() {
    return {
      interval: null
    }
  },
  methods: {
    ...mapActions(["getData"]),

    fetchEpisodes(add = 0) {
      this.getData(this.limit + add)
    },
  },
  mounted() {
    if (this.isEmpty) this.fetchEpisodes()

    this.interval = setInterval(() => {
      this.fetchEpisodes()
    }, 60 * 1000)
  },
  beforeDestroy() {
    clearInterval(this.interval)
  }
}
</script>
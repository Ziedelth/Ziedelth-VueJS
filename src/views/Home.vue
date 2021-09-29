<template>
  <div>
    <div id="jais" class="text-center">
      <img alt="Ziedelth.fr brand" class="rounded-circle border shadow" src="images/jais.jpg" style="width: 7.5rem">
      <h2 class="h2 mt-2 mb-0 fw-bold">Jaïs</h2>
      <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>
      <p class="text mt-0 muted fw-bold">Les dernières sorties</p>

      <BSpinner v-show="isLoaded" type="primary"></BSpinner>

      <div v-show="!isLoaded && episodes.length !== 0" class="container-fluid row g-3">
        <Episode v-for="episode in episodes" :key="episode.eId" :episode="episode"/>
      </div>

      <div class="container-fluid mt-3">
        <b-btn variant="light" @click="fetchEpisodes">Voir plus</b-btn>
        <p class="p-3 text-muted">Vous souhaitez voir les actualités à propos des animés et mangas ?
          <router-link class="text-decoration-none" to="/">Venez ici</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import Episode from "@/components/CardEpisode";
import {mapActions, mapState} from "vuex";

export default {
  components: {Episode},
  computed: {
    ...mapState(['isLoaded', 'limit', 'episodes']),
  },
  methods: {
    ...mapActions(['getEpisodes']),

    fetchEpisodes() {
      this.getEpisodes(this.limit + 9)
    }
  },
  beforeMount() {
    console.log('Before mount')
    if (this.episodes.length === 0) this.getEpisodes()
  }
}
</script>
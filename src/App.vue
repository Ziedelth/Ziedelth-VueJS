<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/">
          <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" src="images/favicon.png"
               width="30">
          Ziedelth.fr
        </router-link>

        <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                class="navbar-toggler" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div id="navbarSupportedContent" class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Accueil</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="about">Contact</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-3">
      <router-view/>
    </div>

    <footer class="footer mt-auto py-3 bg-dark px-3 text-center mt-2">
      <div class="container">
        <p class="text-white-50 fw-bold">&copy; {{ getCurrentYear }} Ziedelth.fr</p>
        <p class="text-muted">Membres : <span class="fw-bold">{{ totalMembers }}</span></p>

        <hr class="text-white-50">

        <div class="row mt-0">
          <div class="col-md-6">
            <p class="text-muted mb-0">Pays pris en charge : <span class="fw-bold">{{ totalCountries }}</span></p>
            <p class="text-muted mt-0 mb-0">Platformes prises en charge : <span class="fw-bold">{{
                totalPlatforms
              }}</span></p>
            <p class="text-muted mt-0">Animés / Mangas reconnus : <span class="fw-bold">{{ totalAnimes }}</span></p>
          </div>

          <div class="col-md-6">
            <p class="text-muted mb-0">Épisodes reconnus : <span class="fw-bold">{{ totalEpisodes }}</span></p>
            <p class="text-muted mt-0">Total cumulés : <span class="fw-bold">{{ toHHMMSS }}</span></p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapActions, mapGetters, mapState} from "vuex";

export default {
  computed: {
    ...mapState(['data', 'episodes', 'totalMembers', 'totalCountries', 'totalPlatforms', 'totalAnimes', 'totalEpisodes', 'totalDuration']),
    ...mapGetters(['isEmpty']),

    getCurrentYear() {
      return new Date().getFullYear();
    },
    toHHMMSS() {
      return Utils.toHHMMSS(this.totalDuration)
    }
  },
  methods: {
    ...mapActions(['getEpisodes']),
  },
  beforeMount() {
    if (this.isEmpty) this.getEpisodes(9)
  }
}
</script>

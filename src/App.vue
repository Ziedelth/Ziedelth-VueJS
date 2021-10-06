<template>
  <div>
    <b-navbar class="px-4" toggleable="lg" type="dark" variant="dark">
      <b-container fluid>
        <router-link class="navbar-brand" to="/">
          <img :src="getFavicon" alt="" class="d-inline-block align-text-top me-2 rounded" height="30"
               width="30">
          Ziedelth.fr
        </router-link>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
          <b-navbar-nav class="me-auto my-2 my-lg-0 navbar-nav-scroll">
            <router-link class="nav-link" to="/">Accueil</router-link>
          </b-navbar-nav>

          <div class="d-flex">
            <router-link class="btn btn-outline-primary me-2" to="/">Inscription</router-link>
            <router-link class="btn btn-outline-success" to="/">Connexion</router-link>
          </div>
        </b-collapse>
      </b-container>
    </b-navbar>

    <b-container class="my-3">
      <router-view/>
    </b-container>

    <footer>
      <b-container class="mt-3 p-3 bg-dark text-center" fluid>
        <p class="text-white-50 fw-bold">&copy; {{ getCurrentYear }} Ziedelth.fr</p>
      </b-container>
    </footer>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  computed: {
    ...mapGetters(["getFavicon"]),

    getCurrentYear() {
      return new Date().getFullYear();
    },
  },
}
</script>

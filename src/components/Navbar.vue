<template>
  <header>
    <div class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/">
          <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" loading="lazy"
               src="images/favicon.jpg" width="30">
          Ziedelth.fr
        </router-link>

        <button aria-controls="nav-collapse" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"
                data-bs-target="#nav-collapse" data-bs-toggle="collapse" type="button">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div id="nav-collapse" class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <router-link class="nav-link" to="/">Accueil</router-link>
            <router-link class="nav-link" to="/animes">Animes</router-link>
          </ul>

          <ul class="navbar-nav mb-2 mb-lg-0">
            <div v-if="!isLogin()" class="d-flex">
              <router-link class="nav-link" to="/register">Inscription</router-link>
              <router-link class="nav-link" to="/login">Connexion</router-link>
            </div>
            <div v-else class="d-flex">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  {{ user.pseudo }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <li><router-link class="dropdown-item" :to="`/member/${user.pseudo}`">Mon profil</router-link></li>
                  <li><router-link class="dropdown-item" to="/settings">Paramètres</router-link></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" @click="logout">Déconnexion</a></li>
                </ul>
              </li>
            </div>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import {mapGetters, mapState} from "vuex";

export default {
  computed: {
    ...mapState(['user'])
  },
  methods: {
    ...mapGetters(['isLogin']),

    logout() {
      if (!this.isLogin())
        return

      this.$session.destroy()
      this.$store.dispatch('setUser', null)
      this.$router.push('/')
    }
  }
}
</script>
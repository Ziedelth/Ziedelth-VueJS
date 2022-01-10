<template>
  <header>
    <div class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/">
          <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" loading="lazy"
               :src="getFavicon()" width="30">
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

          <ul class="navbar-nav">
            <div v-if="isConnected" class="dropdown dropstart">
              <button aria-expanded="false" class="btn" data-bs-toggle="dropdown">
                <img :src="getUserProfile()" alt="User image" class="d-inline-block align-text-top rounded" height="30"
                     loading="lazy" width="30">
              </button>

              <ul class="dropdown-menu dropdown-menu-dark">
                <li>
                  <router-link :to="`/profile/${user.pseudo}`" class="dropdown-item">Mon profil</router-link>
                </li>
                <li v-if="user.role === 100">
                  <router-link class="dropdown-item" to="/">Ajouter des fichiers</router-link>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#" @click="logout">Déconnexion</a></li>
              </ul>
            </div>
            <div v-else>
              <router-link class="btn btn-outline-success me-2" to="/login">Connexion</router-link>
              <router-link class="btn btn-outline-primary" to="/register">Inscription</router-link>
            </div>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import {mapActions, mapGetters, mapState} from "vuex";
import Utils from "@/utils";

export default {
  computed: {
    ...mapGetters(['isConnected']),
    ...mapState(['user'])
  },
  methods: {
    ...mapActions(['setUser']),

    getFavicon() {
      return Utils.getFile("images/favicon.jpg")
    },

    getUserProfile() {
      return Utils.getUserProfile(this.user)
    },

    logout() {
      this.$session.destroy()
      this.setUser(null)
    }
  }
}
</script>
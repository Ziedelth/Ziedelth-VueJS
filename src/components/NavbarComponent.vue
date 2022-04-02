<template>
  <header>
    <b-navbar toggleable="lg" type="dark" variant="dark">
      <b-navbar-brand class="ms-3">
        <figure v-lazyload class="m-0 p-0">
          <img alt="" class="d-inline-block align-text-top me-2 rounded" data-url="images/favicon.jpg" width="30" height="30">
          Ziedelth.fr
        </figure>
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse" class="me-3" />

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav class="ms-3">
          <router-link class="nav-link" to="/">Accueil</router-link>
          <router-link class="nav-link" to="/animes">Animes</router-link>
          <router-link class="nav-link" to="/statistics">Statistiques</router-link>
        </b-navbar-nav>

        <b-navbar-nav class="ms-auto me-5">
          <router-link v-if="!isLogin()" class="nav-link" to="/register">Inscription</router-link>
          <router-link v-if="!isLogin()" class="nav-link" to="/login">Connexion</router-link>

          <b-nav-item-dropdown v-if="isLogin()" :text="user.pseudo" right>
            <router-link :to="`/member/${user.pseudo}`" class="dropdown-item">Mon profil</router-link>
            <router-link class="dropdown-item" to="/settings">Paramètres</router-link>
            <b-dropdown-divider />
            <a class="dropdown-item" @click="logout">Déconnexion</a>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
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
      this.$store.dispatch('setToken', null)
      this.$store.dispatch('setUser', null)
      this.$router.push('/')
    }
  }
}
</script>
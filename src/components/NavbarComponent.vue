<template>
  <header>
    <b-navbar toggleable="lg" type="dark" variant="dark">
      <b-navbar-brand class="ms-5">
        <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" loading="lazy"
             src="images/favicon.jpg" width="30">
        Ziedelth.fr
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse" />

      <b-collapse id="nav-collapse" is-nav class="me-5">
        <b-navbar-nav class="me-auto">
          <router-link class="nav-link" to="/">Accueil</router-link>
          <router-link class="nav-link" to="/animes">Animes</router-link>
          <router-link class="nav-link" to="/statistics">Statistiques</router-link>
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto">
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
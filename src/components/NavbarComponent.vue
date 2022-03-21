<template>
  <header>
<!--    <div class="navbar navbar-expand-lg navbar-dark bg-dark px-4">-->
<!--      <div class="container-fluid">-->
<!--        <router-link class="navbar-brand" to="/">-->
<!--          <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" loading="lazy"-->
<!--               src="images/favicon.jpg" width="30">-->
<!--          Ziedelth.fr-->
<!--        </router-link>-->

<!--        <button aria-controls="nav-collapse" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"-->
<!--                data-bs-target="#nav-collapse" data-bs-toggle="collapse" type="button">-->
<!--          <span class="navbar-toggler-icon"></span>-->
<!--        </button>-->

<!--        <div id="nav-collapse" class="collapse navbar-collapse">-->
<!--          <ul class="navbar-nav me-auto mb-2 mb-lg-0">-->
<!--            <router-link class="nav-link" to="/">Accueil</router-link>-->
<!--            <router-link class="nav-link" to="/animes">Animes</router-link>-->
<!--          </ul>-->

<!--          <ul class="navbar-nav mb-2 mb-lg-0">-->
<!--            <router-link v-if="!isLogin()" class="nav-link" to="/register">Inscription</router-link>-->
<!--            <router-link v-if="!isLogin()" class="nav-link" to="/login">Connexion</router-link>-->

<!--            <li v-if="isLogin()" class="nav-item dropdown">-->
<!--              <a id="navbarDropdown" aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"-->
<!--                 href="#"-->
<!--                 role="button">-->
<!--                {{ user.pseudo }}-->
<!--              </a>-->
<!--              <ul aria-labelledby="navbarDropdown" class="dropdown-menu">-->
<!--                <li>-->
<!--                  <router-link :to="`/member/${user.pseudo}`" class="dropdown-item">Mon profil</router-link>-->
<!--                </li>-->
<!--                <li>-->
<!--                  <router-link class="dropdown-item" to="/settings">Paramètres</router-link>-->
<!--                </li>-->
<!--                <li>-->
<!--                  <hr class="dropdown-divider">-->
<!--                </li>-->
<!--                <li><a class="dropdown-item" @click="logout">Déconnexion</a></li>-->
<!--              </ul>-->
<!--            </li>-->
<!--          </ul>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

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
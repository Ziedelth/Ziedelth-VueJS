<template>
  <header>
    <b-navbar toggleable="lg" type="dark" variant="dark">
      <b-navbar-brand class="ms-3">
        <img alt="" class="m-0 p-0 d-inline-block align-text-top me-2 rounded" src="images/favicon.jpg" width="30" height="30">
        Ziedelth.fr
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse" class="me-3" />

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav class="ms-3">
          <router-link class="nav-link" to="/">Accueil</router-link>
          <router-link class="nav-link" to="/animes">Animes</router-link>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
  </header>
</template>
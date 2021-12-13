<template>
  <header>
    <b-navbar class="px-4" toggleable="lg" type="dark" variant="dark">
      <b-container fluid>
        <router-link class="navbar-brand" to="/">
          <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" loading="lazy"
               src="images/favicon.jpg" width="30">
          Ziedelth.fr
        </router-link>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
          <b-navbar-nav class="me-auto my-2 my-lg-0 navbar-nav-scroll">
            <router-link class="nav-link" to="/">Accueil</router-link>
          </b-navbar-nav>
        </b-collapse>
      </b-container>
    </b-navbar>
  </header>
</template>
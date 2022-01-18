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
        </div>
      </div>
    </div>
  </header>
</template>
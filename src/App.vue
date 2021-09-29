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
  </div>
</template>

<template>
  <div class="container">
    <div class="row g-3 mb-3">
      <div class="col-lg-6 d-flex align-items-center align-content-center">
        <div class="d-block">
          <h1 class="m-0">Oops!</h1>
          <p class="p-0">La page que vous recherchez semble introuvable.</p>
        </div>
      </div>

      <div class="col-lg-6">
        <img alt="404 Image error" class="img-fluid rounded" src="../assets/404.webp">
      </div>
    </div>

    <router-link to="/" class="text-white fw-bold">Retour à l'accueil</router-link>
  </div>
</template>
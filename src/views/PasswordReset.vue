<template>
  <div class="container">
    <h4>Retrouvez votre compte</h4>
    <hr>
    <p>Veuillez entrer votre adresse mail pour rechercher votre compte.</p>
    <input type="text" class="form-control" placeholder="Adresse mail">

    <div class="hstack gap-3 mt-3">
      <button class="btn btn-secondary mx-auto" @click="$router.push('/login')">Annuler</button>
      <button class="btn btn-primary mx-auto">Confirmer</button>
    </div>
  </div>
</template>
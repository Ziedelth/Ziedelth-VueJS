<template>
  <div class="container">
    <h4>Retrouvez votre compte</h4>
    <hr>
    <p>Veuillez entrer votre adresse mail pour rechercher votre compte.</p>
    <input ref="inputEmail" class="form-control" placeholder="Adresse mail" type="email">

    <div class="hstack gap-3 mt-3 mb-3">
      <router-link class="btn btn-secondary mx-auto" to="/login">Annuler</router-link>
      <button class="btn btn-primary mx-auto" @click="submit">Confirmer</button>
    </div>

    <div v-if="success != null && success.length > 0" class="alert-success p-3 text-center rounded fw-bold">{{
        success
      }}
    </div>
    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import Utils from "@/utils";

export default {
  data() {
    return {
      success: null,
      error: null
    }
  },
  methods: {
    async submit() {
      const email = this.$refs.inputEmail.value

      if (Utils.isNullOrEmpty(email))
        return

      await Utils.post(`php/v1/member/password_reset.php`, JSON.stringify({email: email}), (success) => {
        this.success = 'Votre demande a bien été prise en compte, nous vous avons envoyé un mail d\'oubli de mot de passe'
      }, (failed) => {
        console.log(failed)
        this.error = `${failed}`
      })
    }
  }
}
</script>
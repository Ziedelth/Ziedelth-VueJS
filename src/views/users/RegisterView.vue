<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-6 has-validation">
        <input id="pseudo" ref="pseudoInput" class="form-control" placeholder="Pseudonyme" type="text">
        <div ref="pseudoValidation" />
      </div>

      <div class="col-lg-6 has-validation">
        <input ref="emailInput" class="form-control" placeholder="Adresse mail" type="email">
        <div ref="emailValidation" />
      </div>

      <div class="col-lg-12 has-validation">
        <input ref="passwordInput" class="form-control" placeholder="Mot de passe" type="password">
        <div id="emailHelp" class="form-text">Ne partagez jamais votre mot de passe.</div>
        <div ref="passwordValidation" />
      </div>

      <div class="col-lg-12 has-validation">
        <input ref="confirmPasswordInput" class="form-control" placeholder="Confirmation du mot de passe" type="password">
        <div ref="confirmPasswordValidation" />
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitUser">Inscription</button>

      <br><br>
      <span>Vous avez déjà un compte ? <router-link to="/login" class="text-decoration-none link-color">Connectez-vous ici</router-link></span>
    </div>

    <div v-if="success != null && success.length > 0" class="alert-success p-3 text-center rounded fw-bold">{{ success }}</div>
    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapGetters} from "vuex";

export default {
  data() {
    return {
      success: ``,
      error: ``
    }
  },
  mounted() {
    if (this.isLogin()) {
      this.$router.push('/')
    }
  },
  methods: {
    ...mapGetters(['isLogin']),
    invalidInputGroup: function (input, validation, message) {
      input.classList.remove("is-valid")
      input.classList.add("is-invalid")
      validation.classList.remove("valid-feedback")
      validation.classList.add("invalid-feedback")
      validation.textContent = message
    },
    validInputGroup: function (input, validation, message) {
      input.classList.add("is-valid")
      input.classList.remove("is-invalid")
      validation.classList.add("valid-feedback")
      validation.classList.remove("invalid-feedback")
      validation.textContent = message
    },
    async testPseudo(pseudo) {
      if (!(/^\w{4,16}$/.test(pseudo))) {
        this.invalidInputGroup(this.$refs.pseudoInput, this.$refs.pseudoValidation, "Pseudo invalide")
        return false
      }

      let status = false

      await Utils.post(`api/v1/member/exists/pseudo`, JSON.stringify({ pseudo: pseudo }), 200, (success) => {
        if (success.is_exists === true) {
          this.invalidInputGroup(this.$refs.pseudoInput, this.$refs.pseudoValidation, "Pseudonyme déjà utilisé")
          status = false
          return
        }

        this.validInputGroup(this.$refs.pseudoInput, this.$refs.pseudoValidation, "OK")
        status = true
      }, (failed) => {
        this.validInputGroup(this.$refs.pseudoInput, this.$refs.pseudoValidation, "OK")
        status = true
      })

      return status
    },
    async testEmail(email) {
      if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))) {
        this.invalidInputGroup(this.$refs.emailInput, this.$refs.emailValidation, "Adresse mail invalide")
        return false
      }

      let status = null

      await Utils.post(`api/v1/member/exists/email`, JSON.stringify({ email: email }), 200, (success) => {
        if (success.is_exists === true) {
          this.invalidInputGroup(this.$refs.emailInput, this.$refs.emailValidation, "Adresse mail déjà utilisée")
          status = false
          return
        }

        this.validInputGroup(this.$refs.emailInput, this.$refs.emailValidation, "OK")
        status = true
      }, (failed) => {
        this.validInputGroup(this.$refs.emailInput, this.$refs.emailValidation, "OK")
        status = true
      })

      return status
    },
    testPassword(password) {
      if (Utils.isNullOrEmpty(password)) {
        this.invalidInputGroup(this.$refs.passwordInput, this.$refs.passwordValidation, "Mot de passe invalide")
        return false
      }

      this.validInputGroup(this.$refs.passwordInput, this.$refs.passwordValidation, "OK")
      return true
    },
    testConfirmationPassword(password, confirmPassword) {
      if (Utils.isNullOrEmpty(confirmPassword)) {
        this.invalidInputGroup(this.$refs.confirmPasswordInput, this.$refs.confirmPasswordValidation, "Mot de passe invalide")
        return false
      }

      if (confirmPassword !== password) {
        this.invalidInputGroup(this.$refs.confirmPasswordInput, this.$refs.confirmPasswordValidation, "Les mots de passes ne sont pas identiques")
        return false
      }

      this.validInputGroup(this.$refs.confirmPasswordInput, this.$refs.confirmPasswordValidation, "OK")
      return true
    },

    async submitUser() {
      const pseudo = this.$refs.pseudoInput.value
      const email = this.$refs.emailInput.value
      const password = this.$refs.passwordInput.value
      const confirmPassword = this.$refs.confirmPasswordInput.value

      const testPseudo = await this.testPseudo(pseudo)
      const testEmail = await this.testEmail(email)
      const testPassword = this.testPassword(password)
      const testConfirmationPassword = this.testConfirmationPassword(password, confirmPassword)

      if (!(testPseudo && testEmail && testPassword && testConfirmationPassword))
        return

      await Utils.post(`api/v1/member/register`, JSON.stringify({
        pseudo: pseudo,
        email: email,
        password: password
      }), (success) => {
        if ("error" in success) {
          this.error = `${success.error}`
          return
        }

        this.success = `Un mail de confirmation vous a été envoyé à l'adresse mail suivante : ${email}. Veuillez le confirmer, vérifier aussi vos courriers indésirables.<br/>Vous ne pourrez pas vous connecter tant que celle-ci ne sera pas valider.`
        setTimeout(() => this.$router.push('/login'), 10000)
      }, (failed) => {
        console.log(failed)
        this.error = `${failed}`
      })
    }
  }
}
</script>
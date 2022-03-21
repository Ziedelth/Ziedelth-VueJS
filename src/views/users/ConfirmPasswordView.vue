<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-12 has-validation">
        <input ref="passwordInput" class="form-control" placeholder="Mot de passe" type="password">
        <div id="emailHelp" class="form-text">Ne partagez jamais votre mot de passe.</div>
        <div ref="passwordValidation" />
      </div>

      <div class="col-lg-12 has-validation mt-3">
        <input ref="confirmPasswordInput" class="form-control" placeholder="Confirmation du mot de passe" type="password">
        <div ref="confirmPasswordValidation" />
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitPassword" ref="submitPassword">Changer mon mot de passe</button>
    </div>

    <div v-if="success != null && success.length > 0" class="alert-success p-3 text-center rounded fw-bold">{{ success }}</div>
    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import Utils from "@/utils";

export default {
  name: "ConfirmPasswordView",
  data() {
    return {
      success: ``,
      error: ``,
    }
  },
  methods: {
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
    async submitPassword() {
      const hash = this.$route.params.hash
      this.$refs.submitPassword.disabled = true

      const password = this.$refs.passwordInput.value
      const confirmPassword = this.$refs.confirmPasswordInput.value

      const testPassword = this.testPassword(password)
      const testConfirmationPassword = this.testConfirmationPassword(password, confirmPassword)

      if (!(testPassword && testConfirmationPassword)) {
        this.$refs.submitPassword.disabled = false
        return
      }

      await Utils.post(`api/v1/member/confirm_password_reset`, JSON.stringify({
        hash: hash,
        password: password
      }), (success) => {
        if ("error" in success) {
          this.$refs.submitPassword.disabled = true
          this.error = `${success.error}`
          return
        }

        this.error = null
        this.success = `Votre mot de passe a bien été changé`
        setTimeout(() => this.$router.push('/login'), 10000)
      }, (failed) => {
        this.$refs.submitButton.disabled = false
        console.log(failed)
        this.error = `${failed}`
      })
    }
  }
}
</script>

<style scoped>

</style>
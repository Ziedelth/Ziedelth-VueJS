<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-12 has-validation">
        <input ref="emailInput" class="form-control" placeholder="Adresse mail" type="email">
        <div ref="emailValidation" />
      </div>

      <div class="col-lg-12 has-validation">
        <input ref="passwordInput" class="form-control" placeholder="Mot de passe" type="password">
        <div class="form-text">Ne partagez jamais votre mot de passe.</div>
        <div ref="passwordValidation" />
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitUser" ref="submitButton">Connexion</button>

      <div class="mt-3 mb-3">
        <router-link class="btn btn-outline-secondary" to="/password_reset">Mot de passe oublié ?</router-link>
      </div>

      <span>Vous n'avez pas encore de compte ? <router-link to="/register" class="text-decoration-none link-color">Inscrivez-vous ici</router-link></span>
    </div>

    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";
import Utils from "@/utils";

export default {
  data() {
    return {
      error: ``,
      interval: null,
    }
  },
  mounted() {
    if (this.isLogin()) {
      this.$router.push('/')
      return
    }

    this.interval = setInterval(() => {
      if (this.isLogin()) {
        this.$router.push('/')
        return
      }
    }, 5000)
  },
  destroyed() {
    clearInterval(this.interval);
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
    async testEmail(email) {
      if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))) {
        this.invalidInputGroup(this.$refs.emailInput, this.$refs.emailValidation, "Adresse mail invalide")
        return false
      }

      let status = null

      await Utils.post(`api/v1/member/exists/email`, JSON.stringify({ email: email }), 200, (success) => {
        if (success.is_exists === false) {
          this.invalidInputGroup(this.$refs.emailInput, this.$refs.emailValidation, "Adresse mail non utilisée")
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

    async submitUser() {
      this.$refs.submitButton.disabled = true

      const email = this.$refs.emailInput.value
      const password = this.$refs.passwordInput.value

      const testEmail = await this.testEmail(email)
      const testPassword = this.testPassword(password)

      if (!(testEmail && testPassword)) {
        this.$refs.submitButton.disabled = false
        return
      }

      await Utils.post(`api/v1/member/login/user`, JSON.stringify({
        email: email,
        password: password
      }), (success) => {
        if ("error" in success) {
          this.error = `${success.error}`
          this.$refs.submitButton.disabled = false
          return
        }

        this.$session.start()
        this.$session.set('token', success.token)
        this.$store.dispatch('setToken', success.token)
        this.$store.dispatch('setUser', success.user)
        this.$router.push('/')
        this.$refs.submitButton.disabled = false
      }, (failed) => {
        this.$refs.submitButton.disabled = false
        this.error = `${failed}`
      })
    }
  }
}
</script>
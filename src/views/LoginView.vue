<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-12">
        <div class="input-group mb-3">
          <span id="basic-addon1" class="input-group-text">@</span>
          <input ref="pseudoInput" class="form-control" placeholder="Pseudonyme" type="text">
        </div>
      </div>

      <div class="col-lg-12">
        <input ref="passwordInput" class="form-control" placeholder="Mot de passe" type="password">
        <div id="emailHelp" class="form-text">Ne partagez jamais votre mot de passe.</div>
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitUser">Connexion</button>

      <div class="mt-3 mb-3">
        <router-link class="btn btn-outline-secondary" to="/password_reset">Mot de passe oublié ?</router-link>
      </div>

      <span>Vous n'avez pas encore de compte ? <router-link to="/register">Inscrivez-vous ici</router-link></span>
    </div>

    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import {sha512} from "js-sha512";
import Utils from "@/utils";
import {mapGetters} from "vuex";

export default {
  data() {
    return {
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

    async submitUser() {
      const pseudo = this.$refs.pseudoInput.value
      const password = sha512(this.$refs.passwordInput.value)

      if (pseudo.length < 4 || pseudo.length > 16) {
        console.log('Invalid pseudo')
        this.error = `Invalid pseudo`
        return
      }

      if (Utils.isNullOrEmpty(password)) {
        console.log('Password can not be empty')
        this.error = `Password can not be empty`
        return
      }

      await Utils.post(`php/v1/member/login.php`, JSON.stringify({
        pseudo: pseudo,
        password: password
      }), 200, (success) => {
        this.$session.start()
        this.$session.set('token', success.token)
        this.$store.dispatch('setToken', success.token)

        Utils.post(`php/v1/member/get_user.php`, JSON.stringify({token: success.token}), 200, (success) => {
          this.$store.dispatch('setUser', success)
          this.$router.push('/')
        }, (failed) => {
          this.error = `${failed}`
        })
      }, (failed) => {
        this.error = `${failed}`
      })
    }
  }
}
</script>
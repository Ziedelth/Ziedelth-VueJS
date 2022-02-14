<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-6">
        <input ref="mailInput" class="form-control" placeholder="Adresse mail" type="email">
      </div>

      <div class="col-lg-6">
        <div class="input-group mb-3">
          <span id="basic-addon1" class="input-group-text">@</span>
          <input ref="pseudoInput" class="form-control" placeholder="Pseudonyme" type="text">
        </div>
      </div>

      <div class="col-lg-12">
        <input ref="passwordInput" class="form-control" placeholder="Mot de passe" type="password">
        <div id="emailHelp" class="form-text">Ne partagez jamais votre mot de passe.</div>
      </div>

      <div class="col-lg-12">
        <input ref="confirmPasswordInput" class="form-control" placeholder="Confirmation du mot de passe"
               type="password">
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitUser">Inscription</button>

      <br><br>
      <span>Vous avez déjà un compte ? <router-link to="/login">Connectez-vous ici</router-link></span>
    </div>

    <div v-if="success != null && success.length > 0" class="alert-success p-3 text-center rounded fw-bold">{{
        success
      }}
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

    async submitUser() {
      const email = this.$refs.mailInput.value
      const pseudo = this.$refs.pseudoInput.value
      const password = sha512(this.$refs.passwordInput.value)
      const confirmPassword = sha512(this.$refs.confirmPasswordInput.value)

      if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))) {
        console.log('Invalid email')
        this.error = `Invalid email`
        return
      }

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

      if (password !== confirmPassword) {
        console.log('Invalid password or confirm password')
        this.error = `Invalid password or confirm password`
        return
      }

      await Utils.post(`php/v1/register.php`, JSON.stringify({
        email: email,
        pseudo: pseudo,
        password: password
      }), 201, (success) => {
        this.success = `Un mail de confirmation vous a été envoyé à l'adresse mail suivante : ${email}. Veuillez le confirmer, vérifier aussi vos courriers indésirables.<br/>Vous ne pourrez pas vous connecter tant que celle-ci ne sera pas valider.`
        setTimeout(() => this.$router.push('/login'), 10000)
      }, (failed) => {
        this.error = failed
      })
    }
  }
}
</script>
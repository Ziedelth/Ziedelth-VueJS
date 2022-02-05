<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-6">
        <input type="email" class="form-control" placeholder="Adresse mail" ref="mailInput">
      </div>

      <div class="col-lg-6">
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">@</span>
          <input type="text" class="form-control" placeholder="Pseudonyme" ref="pseudoInput">
        </div>
      </div>

      <div class="col-lg-12">
        <input type="password" class="form-control" placeholder="Mot de passe" ref="passwordInput">
        <div id="emailHelp" class="form-text">Ne partagez jamais votre mot de passe.</div>
      </div>

      <div class="col-lg-12">
        <input type="password" class="form-control" placeholder="Confirmation du mot de passe" ref="confirmPasswordInput">
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitUser">Inscription</button>
    </div>

    <div class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import {sha512} from "js-sha512";
import Utils from "@/utils";

export default {
  data() {
    return {
      error: ``
    }
  },
  methods: {
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

      if (password !== confirmPassword) {
        console.log('Invalid password or confirm password')
        this.error = `Invalid password or confirm password`
        return
      }

      try {
        const response = await fetch(Utils.getLocalFile("php/v1/register.php"), {
          method: "POST",
          body: JSON.stringify({ email: email, pseudo: pseudo, password: password })
        })

        console.log(response.statusText)

        if (response.status === 201) {
          await this.$router.push('/')
        } else {
          this.error = `${response.statusText}`
        }
      } catch (exception) {
        this.error = `${exception}`
      }
    }
  }
}
</script>
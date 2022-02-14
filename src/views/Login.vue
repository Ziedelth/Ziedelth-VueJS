<template>
  <div class="container text-start">
    <div class="row g-3 mb-3">
      <div class="col-lg-12">
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">@</span>
          <input type="text" class="form-control" placeholder="Pseudonyme" ref="pseudoInput">
        </div>
      </div>

      <div class="col-lg-12">
        <input type="password" class="form-control" placeholder="Mot de passe" ref="passwordInput">
        <div id="emailHelp" class="form-text">Ne partagez jamais votre mot de passe.</div>
      </div>
    </div>

    <div class="w-100 text-center mb-3">
      <button class="btn btn-primary" @click="submitUser">Connexion</button>
    </div>

    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>

<script>
import {sha512} from "js-sha512";
import Utils from "@/utils";
import {mapGetters, mapState} from "vuex";

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

      try {
        const response = await fetch(Utils.getLocalFile("php/v1/login.php"), {
          method: "POST",
          body: JSON.stringify({ pseudo: pseudo, password: password })
        })

        const json = await response.json()
        console.log(response.statusText)

        if (response.status !== 200) {
          this.error = `${json.error}`
          return
        }

        this.$session.start()
        this.$session.set('token', json.token)
      } catch (exception) {
        this.error = `${exception}`
      }

      try {
        const response = await fetch(Utils.getLocalFile("php/v1/get_user.php"), {
          method: "POST",
          body: JSON.stringify({ token: this.$session.get('token') })
        })

        const json = await response.json()
        console.log(response.statusText)

        if (response.status !== 200) {
          this.error = `${json.error}`
          return
        }

        await this.$store.dispatch('setUser', json)
        await this.$router.push('/')
      } catch (exception) {
        this.error = `${exception}`
      }
    }
  }
}
</script>
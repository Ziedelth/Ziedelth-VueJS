<template>
  <div class="container">
    <div class="input-group flex-nowrap mb-3">
      <span id="addon-wrapping" class="input-group-text">@</span>
      <input ref="pseudo" aria-describedby="addon-wrapping" aria-label="Pseudonyme" class="form-control"
             placeholder="Pseudonyme" type="text">
    </div>

    <hr>

    <div class="mb-3 row">
      <label class="col-sm-2 col-form-label" for="inputPassword">Mot de passe</label>
      <div class="col-sm-10">
        <input id="inputPassword" ref="password" class="form-control" type="password">
      </div>
    </div>

    <div class="text-center">
      <button class="btn btn-primary mb-2" @click="login">Se connecter</button>
      <p class="text-muted mb-1">Mot de passe oublié ?
        <router-link to="/forgot_password">Changez-le ici</router-link>
      </p>
      <p class="text-muted">Vous n'avez pas encore de compte ?
        <router-link to="/register">Inscrivez-vous ici</router-link>
      </p>

      <div v-if="isLoading" class="spinner-border my-2" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>

      <div v-else>
        <p v-if="error !== null" class="alert-danger text-danger rounded fw-bold p-2">{{ error }}</p>
        <p v-if="success !== null" class="alert-success text-success  rounded fw-bold p-2">{{ success }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import {sha512} from "js-sha512";
import Utils from "@/utils";
import {mapActions} from "vuex";

export default {
  data() {
    return {
      isLoading: false,
      success: null,
      error: null
    }
  },
  methods: {
    ...mapActions(['setUser']),

    async login() {
      if (!this.isLoading) {
        this.isLoading = true
        const pseudo = this.$refs.pseudo.value
        const password = this.$refs.password.value

        if (pseudo.length > 0 && password.length > 0) {
          if (pseudo.length >= 4 && pseudo.length <= 16) {
            const regex = RegExp("^(?=.*[A-Z])(?=.*[a-z])(?=.*?[0-9])(?=.*?[!@#\\$&*~]).{8,}$")

            if (password.match(regex)) {
              const hashedPassword = sha512(this.$refs.password.value);

              try {
                const response = await fetch(Utils.getLocalFile("php/ziedelth/login.php"), {
                  method: 'POST',
                  body: JSON.stringify({pseudo: pseudo, password: hashedPassword})
                })

                console.log(response)
                const json = await response.json();
                console.log(json)

                if (response.status === 201) {
                  this.success = "Vous êtes maintenant connecté !";

                  this.$session.start()
                  this.$session.set('token', json.token)
                  this.setUser(json)
                  await this.$router.push('/')
                } else
                  this.error = json.error;
              } catch (exception) {
                this.error = exception;
              }
            } else {
              this.error = "Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre, un caractère spéciale et doit faire au minimum 8 caractères";
            }
          } else {
            this.error = "Votre pseudonyme doit faire au minimum 4 caractères et au maximum 16 caractères";
          }
        } else {
          this.error = "Veuillez remplir tout les champs"
        }

        this.isLoading = false
      }
    }
  }
}
</script>
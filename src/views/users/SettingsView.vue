<template>
  <div class="container">
    <div class="mb-3">
      <h4 class="mb-3">Image de profil</h4>
      <label class="w-25 cursor-pointer" for="inputImage"><img :src="getImage()" alt="Member image"
                                                               class="img-fluid rounded mb-3 "></label>
      <input id="inputImage" ref="inputImage" accept="image/*" class="d-none" type="file"
             @change="submitNewImage($event)">
    </div>

    <hr>

    <div class="mb-3 container text-start">
      <label class="form-label" for="inputAbout">À propos</label>
      <input id="inputAbout" ref="inputAbout" class="form-control" type="text" v-model="user.about">
    </div>

    <div class="mb-3">
      <button class="btn btn-primary" @click="submitNew" ref="submitButton">Modifier</button>
      <br>
      <br>
      <button class="btn btn-outline-danger" @click="submitDelete" ref="deleteButton"><b-icon-trash-fill class="me-2" />Supprimer mon compte</button>
    </div>
    <br>
    <label v-if="success != null && success.length > 0" class="mt-3 alert-success p-3 text-center rounded fw-bold">{{
        success
      }}</label>
    <label v-if="error != null && error.length > 0" class="mt-3 alert-danger p-3 text-center rounded fw-bold">{{
        error
      }}</label>
  </div>
</template>

<script>
import {mapGetters, mapState} from "vuex";
import Utils from "@/utils";

export default {
  computed: {
    ...mapState(['token', 'user'])
  },
  data() {
    return {
      error: null,
      success: null,
      interval: null,
    }
  },
  mounted() {
    if (!this.isLogin()) {
      this.$router.push('/')
      return
    }

    this.interval = setInterval(() => {
      if (!this.isLogin()) {
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

    showSuccess: function (message) {
      this.success = message
      setTimeout(() => this.success = null, 5000)
    },

    getImage() {
      if (Utils.isNullOrEmpty(this.user.image))
        return 'images/default_member.jpg'

      return this.user.image
    },

    async submitNewImage(event) {
      if (Utils.isNullOrEmpty(event.target.files))
        return

      const form = new FormData()
      form.append('token', this.token)
      form.append('file', event.target.files[0])

      await Utils.file(`api/v1/member/update/image`, form, (success) => {
        if ("error" in success) {
          this.error = `${success.error}`
          return
        }

        this.$store.dispatch('setUser', success)
        this.showSuccess(`Votre image de profil a bien été mise à jour`)
      }, (failed) => {
        this.error = `${failed}`
      })
    },
    async submitNew() {
      this.$refs.submitButton.disabled = true

      await Utils.put(`api/v1/member/update`, JSON.stringify({
        token: this.token,
        about: this.$refs.inputAbout.value
      }), (success) => {
        this.$refs.submitButton.disabled = false

        if ("error" in success) {
          this.error = `${success.error}`
          return
        }

        this.$store.dispatch('setUser', success)
        this.showSuccess(`Votre profil a bien été mis à jour`)
      }, (failed) => {
        this.error = `${failed}`
      })
    },
    async submitDelete() {
      this.$refs.deleteButton.disabled = true

      await Utils.post(`api/v1/member/delete`, JSON.stringify({
        token: this.token
      }), (success) => {
        this.$refs.deleteButton.disabled = false

        if ("error" in success) {
          this.error = `${success.error}`
          return
        }

        this.showSuccess(`Votre demande de suppression de compte a bien été prise en compte, un mail de confirmation vous a été envoyé`)
      }, (failed) => {
        this.$refs.deleteButton.disabled = false
        this.error = `${failed}`
      })
    }
  },
}
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
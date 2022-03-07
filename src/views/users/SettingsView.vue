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
      <input id="inputAbout" ref="inputAbout" class="form-control" type="text">
    </div>

    <div class="mb-3">
      <button class="btn btn-primary" @click="submitNew()">Modifier</button>
    </div>
    <div class="mb-3">
      <button class="btn btn-outline-danger"><i class="bi bi-trash-fill me-2"></i>Supprimer mon compte</button>
    </div>

    <br>
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
    }
  },
  methods: {
    ...mapGetters(['isLogin']),

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

      await Utils.post(`php/v1/member/upload_image.php`, form, (success) => {
        this.$store.dispatch('setUser', success)
      }, (failed) => {
        this.error = `${failed}`
      })
    },

    async submitNew() {
      await Utils.post(`php/v1/member/update.php`, JSON.stringify({
        token: this.token,
        about: this.$refs.inputAbout.value
      }), (success) => {
        this.$store.dispatch('setUser', success)
      }, (failed) => {
        this.error = `${failed}`
      })
    }
  },
  mounted() {
    if (!this.isLogin()) {
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
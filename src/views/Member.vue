<template>
  <div class="container">
    <p v-if="member == null && error != null" class="alert-danger fw-bold p-2 rounded">{{ error }}</p>

    <div v-else>
      <img :src="getImage()" alt="Member image" class="img-fluid rounded-circle mb-3 w-10">
      <h3 class="fw-bold"><i v-if="isAdmin()" class="bi bi-patch-check-fill me-1 text-danger"></i> {{ member.pseudo }}</h3>
      <p class="mb-1">Inscription il y a {{ timeSince() }}</p>
      <p class="fw-light">{{ member.about }}</p>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

export default {
  data() {
    return {
      pseudo: this.$route.params.pseudo,
      error: null,
      member: null,
    }
  },
  methods: {
    getImage() {
      if (Utils.isNullOrEmpty(this.member.image))
        return 'images/members/default_member.jpg'

      return this.member.image
    },
    timeSince() {
      return Utils.timeSince(new Date(this.member.timestamp).getTime())
    },
    isAdmin() {
      return this.member.role >= 100
    },
  },
  async mounted() {
    try {
      const response = await fetch(Utils.getLocalFile(`php/v1/get_user.php?pseudo=${this.pseudo}`))

      const json = await response.json()
      console.log(response.statusText)

      if (response.status !== 200) {
        this.error = `${json.error}`
        return
      }

      this.member = json
    } catch (exception) {
      this.error = `${exception}`
    }
  }
}
</script>

<style scoped>
.w-10 {
  width: 10% !important
}
</style>
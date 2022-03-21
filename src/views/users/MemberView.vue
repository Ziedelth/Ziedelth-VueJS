<template>
  <div class="container">
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="member == null && error != null" class="alert-danger fw-bold p-2 rounded">{{ error }}</p>

      <div v-else>
        <img :src="getImage()" alt="Member image" class="w-25 img-fluid rounded mb-3">
        <h3 class="fw-bold">
          <b-icon-patch-check-fill variant="danger" v-if="isAdmin()" />
          {{ member.pseudo }}
        </h3>
        <p class="mb-1">Inscription il y a {{ timeSince() }}</p>
        <p class="fw-light">{{ member.about }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

const LoadingComponent = () => import("@/components/LoadingComponent");

export default {
  components: {
    LoadingComponent
  },
  data() {
    return {
      isLoading: true,
      error: null,
      member: null,
    }
  },
  methods: {
    getImage() {
      if (Utils.isNullOrEmpty(this.member.image))
        return 'images/default_member.jpg'

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
    this.isLoading = true

    await Utils.get(`api/v1/member/${this.$route.params.pseudo}`, (success) => {
      if ("error" in success) {
        this.error = `${success.error}`
        return
      }

      this.member = success
    }, (failed) => {
      this.error = `${failed}`
    })

    this.isLoading = false
  }
}
</script>
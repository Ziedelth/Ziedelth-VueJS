<template>
  <div>
    <LoadingComponent v-if="isLoading"/>

    <div v-if="error != null && error.length > 0" class="alert-danger p-3 text-center rounded fw-bold">{{ error }}</div>
  </div>
</template>
<script>
const LoadingComponent = () => import("@/components/LoadingComponent");
import Utils from "@/utils";

export default {
  components: {LoadingComponent},
  data() {
    return {
      isLoading: true,
      action: ``,
      error: ``
    }
  },
  async mounted() {
    this.isLoading = true
    const hash = this.$route.params.hash

    await Utils.get(`api/v1/member/validate_action/${hash}`, (success) => {
      if ("error" in success) {
        this.error = `${success.error}`
        return
      }

      switch (success.object.action) {
        case 'VERIFY_EMAIL':
          this.$router.push('/login')
          break;
        case 'PASSWORD_RESET':
          this.action = ``
          break;
        case 'DELETE_ACCOUNT':
          this.$session.destroy()
          this.$store.dispatch('setToken', null)
          this.$store.dispatch('setUser', null)
          this.$router.push('/')
          break;
        default:
          this.$router.push('/')
          break;
      }
    }, (failed) => {
      this.error = `${failed}`
    })

    this.isLoading = false
  }
}
</script>
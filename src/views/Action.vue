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

    await Utils.post(`php/v1/member/action.php`, JSON.stringify({hash: hash}), 200, (json) => {
      if (json.object.action === 'VERIFY_EMAIL')
        this.$router.push('/login')
      else if (json.object.action === 'PASSWORD_RESET')
        this.action = `PASSWORD_RESET`
    }, (failed) => {
      this.error = `${failed}`
    })

    this.isLoading = false
  }
}
</script>
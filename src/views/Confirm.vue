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
      error: ``
    }
  },
  async mounted() {
    const hash = this.$route.params.hash

    await Utils.post(`php/v1/confirm.php`, JSON.stringify({hash: hash}), 200, (json) => {
      this.$router.push('/login')
    }, (failed) => {
      this.error = failed
    })

    this.isLoading = false
  }
}
</script>
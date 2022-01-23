<template>
  <div v-if="isLoading">
    <div class="spinner-border my-2" role="status"/>
    <p class="fw-bold">Chargement...</p>
  </div>
</template>
<script>
export default {
  name: 'LoadingComponent',
  props: {
    isLoading: {}
  }
}
</script>

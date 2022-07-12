<template>
  <div v-if="isLoading" class="my-2">
    <img alt="Loading icon" src="../assets/svg/loading.svg" class="spinner">
    <p class="fw-bold">Chargement...</p>
  </div>
</template>
<script>
export default {
  name: 'LoadingComponent',
  props: {
    isLoading: {
      type: Boolean
    }
  }
}
</script>

<style>
.spinner {
  animation-name: spin;
  animation-duration: 2500ms;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
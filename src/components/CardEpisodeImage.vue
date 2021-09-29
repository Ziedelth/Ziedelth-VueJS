<template>
  <a :href="currentEpisode.url" target="_blank"><img :src="currentEpisode.image" alt="Anime image episode"
                                                     class="mb-2 rounded img-fluid w-100 mt-2"></a>
</template>

<script>
export default {
  name: "CardEpisodeImage",
  props: {
    currentEpisode: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>

</style>
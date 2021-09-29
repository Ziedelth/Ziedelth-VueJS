<template>
  <div class="row">
    <div class="col-9 text-truncate">
      <div class="d-flex align-items-center align-content-center">
        <a :href="currentEpisode.platformUrl"><img :alt="currentEpisode.platformName"
                                                   :src="currentEpisode.platformImage" class="platform-thumbnail me-2"></a>{{
          currentEpisode.platformName
        }}
      </div>

      <div class="text-start">
        <p class="card-title fw-bold mb-1">{{ currentEpisode.animeName }}</p>
        <CardEpisodeInfo :current-episode="currentEpisode"/>
      </div>
    </div>

    <div class="col-3 text-end">
      <img alt="Anime brand" class="img-fluid rounded" src="images/jais.jpg">
    </div>
  </div>
</template>

<script>
import CardEpisodeInfo from "@/components/CardEpisodeInfo";

export default {
  name: "CardEpisodeHeader",
  components: {
    CardEpisodeInfo
  },
  props: {
    currentEpisode: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>
.platform-thumbnail {
  width: 4vh;
  height: 4vh;
  border-radius: 50%;
  margin: .25rem;
}
</style>
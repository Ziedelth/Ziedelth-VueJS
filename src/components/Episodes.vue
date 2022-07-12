<template>
  <div class="row row-cols-lg-4 g-3 d-flex justify-content-center text-center mb-3">
    <div v-for="episode in episodes" class="col-lg" :key="episode.episode_id">
      <EpisodeComponent :episode="episode"/>
    </div>
  </div>
</template>

<script>
import EpisodeComponent from "@/components/EpisodeComponent";

export default {
  name: 'Episodes',
  components: {
    EpisodeComponent
  },
  props: {
    episodes: {},
  }
}
</script>
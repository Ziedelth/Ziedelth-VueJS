<template>
  <div>
    <p class="card-text">
      {{ currentEpisode.title === null ? "¯\\_(ツ)_/¯" : currentEpisode.title }}
      <br>
      {{ currentEpisode.countrySeason }} {{ currentEpisode.season }} •
      {{ currentEpisode.episodeType === 'FILM' ? currentEpisode.countryFilm : currentEpisode.countryEpisode }}
      {{ currentEpisode.number }}
      <br>
      <b-icon-camera-reels-fill/>
      {{ toHHMMSS }}
    </p>
  </div>
</template>

<script>
import Utils from "@/utils";

export default {
  name: "CardEpisodeInfo",
  props: {
    currentEpisode: {
      type: Object,
      required: true
    }
  },
  computed: {
    toHHMMSS() {
      return Utils.toHHMMSS(this.currentEpisode.duration.toString())
    }
  },
}
</script>

<style scoped>

</style>
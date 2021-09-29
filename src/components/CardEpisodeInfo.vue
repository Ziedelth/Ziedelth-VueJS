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
      const sec_num = parseInt(this.currentEpisode.duration.toString(), 10); // don't forget the second param
      let hours = Math.floor(sec_num / 3600);
      let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
      let seconds = sec_num - (hours * 3600) - (minutes * 60);

      if (hours < 10) {
        hours = "0" + hours;
      }
      if (minutes < 10) {
        minutes = "0" + minutes;
      }
      if (seconds < 10) {
        seconds = "0" + seconds;
      }

      return (hours >= 1 ? hours + ':' : '') + minutes + ':' + seconds;
    }
  }
}
</script>

<style scoped>

</style>
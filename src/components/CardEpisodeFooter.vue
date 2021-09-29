<template>
  <div class="d-flex">
    <div class="m-0 me-auto justify-content-start">
      {{ currentEpisode.langType === "VOICE" ? currentEpisode.countryDubbed : currentEpisode.countrySubtitles }} • Il y
      a {{ timeSince }}
    </div>

    <div v-if="typeof currentEpisode.number !== 'undefined'">
      <div class="m-0 ms-auto justify-content-end">
        <div class="d-inline me-2">
          <b-icon-check2/>
          0
        </div>

        <div class="d-inline">
          <b-icon-heart-fill/>
          0
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CardEpisodeFooter",
  props: {
    currentEpisode: {
      type: Object,
      required: true
    }
  },
  computed: {
    now() {
      return new Date()
    },
    getEpisodeDateTime() {
      return new Date(this.currentEpisode.releaseDate).getTime()
    },
    timeSince() {
      const seconds = Math.floor((this.now - this.getEpisodeDateTime) / 1000);
      let interval = seconds / 31536000;
      if (interval > 1) return Math.floor(interval) + " an" + (interval >= 2 ? "s" : "");
      interval = seconds / 2592000;
      if (interval > 1) return Math.floor(interval) + " mois";
      interval = seconds / 86400;
      if (interval > 1) return Math.floor(interval) + " jour" + (interval >= 2 ? "s" : "");
      interval = seconds / 3600;
      if (interval > 1) return Math.floor(interval) + " heure" + (interval >= 2 ? "s" : "");
      interval = seconds / 60;
      if (interval > 1) return Math.floor(interval) + " minute" + (interval >= 2 ? "s" : "");
      return Math.floor(seconds) + " seconde" + (seconds >= 2 ? "s" : "");
    }
  }
}
</script>
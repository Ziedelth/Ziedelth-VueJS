<template>
  <div class="border-color rounded px-3 py-2 bg-dark">
    <div class="d-flex align-items-center align-content-center fw-bold">
      <a :href="episode.platform.url" target="_blank" class="text-decoration-none text-white">
        <img :src="episode.platform.image" alt="Platform image" class="platform-thumbnail me-2"/>
      </a>

      <router-link :to="`/anime/${episode.anime.url}`" class="fw-bold link-color text-truncate">{{
          episode.anime.name
        }}
      </router-link>
    </div>

    <div class="text-start text-truncate">
      <p class="card-text">
        <span class="fw-bold">{{ toTitle() }}</span>
        <br>
        {{ toDescription() }}
        <br>
        <img :alt="`Episode duration ${toHHMMSS()}`"
             height="20"
             src="../assets/svg/camera.svg"
             width="20">
        {{ toHHMMSS() }}
      </p>
    </div>

    <a :href="episode.url" target="_blank">
      <img :src="getAttachment(episode.image)" alt="Episode image" class="m-0 p-0 mb-2 rounded img-fluid w-100 mt-2" width="1920" height="1080">
    </a>

    <div class="d-flex">
      <div class="m-0 me-auto justify-content-start">
        Il y a {{ toTimeSince() }}
      </div>
    </div>
  </div>
</template>

<script>
import Const from "@/const";

export default {
  name: 'EpisodeComponent',
  props: {
    episode: {},
  },
  methods: {
    getAttachment(src) {
      if (src == null || src.startsWith("http")) {
        return src;
      }

      return Const.ATTACHMENTS_URL + src;
    },
    toTitle() {
      const string = this.episode.title

      if (string == null || string === "") {
        return "＞﹏＜";
      }

      return string
    },
    toDescription() {
      return this.episode.anime.country.season + " " + this.episode.season + " • " + this.episode.episodeType.fr + " " + this.episode.number + " " + this.episode.langType.fr
    },
    toHHMMSS() {
      const sec_num = parseInt(this.episode.duration, 10)

      if (sec_num <= 0)
        return '??:??'

      let hours = Math.floor(sec_num / 3600)
      let minutes = Math.floor((sec_num - (hours * 3600)) / 60)
      let seconds = sec_num - (hours * 3600) - (minutes * 60)

      const options = {minimumIntegerDigits: 2}
      return (hours >= 1 ? hours.toLocaleString('fr-FR', options) + ':' : '') + minutes.toLocaleString('fr-FR', options) + ':' + seconds.toLocaleString('fr-FR', options)
    },
    toTimeSince() {
      const seconds = Math.floor((new Date().getTime() - new Date(this.episode.releaseDate).getTime()) / 1000)
      let interval = seconds / 31536000
      if (interval > 1) return Math.floor(interval) + " an" + (interval >= 2 ? "s" : "")
      interval = seconds / 2592000
      if (interval > 1) return Math.floor(interval) + " mois"
      interval = seconds / 86400
      if (interval > 1) return Math.floor(interval) + " jour" + (interval >= 2 ? "s" : "")
      interval = seconds / 3600
      if (interval > 1) return Math.floor(interval) + " heure" + (interval >= 2 ? "s" : "")
      interval = seconds / 60
      if (interval > 1) return Math.floor(interval) + " minute" + (interval >= 2 ? "s" : "")
      return Math.floor(seconds) + " seconde" + (seconds >= 2 ? "s" : "")
    }
  }
}
</script>

<style>
.platform-thumbnail {
  width: 2.5vh;
  height: 2.5vh;
  border-radius: 50%;
  margin: .25rem;
}
</style>


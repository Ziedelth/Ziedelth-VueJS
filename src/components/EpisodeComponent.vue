<template>
  <div class="border-color rounded p-3 bg-dark">
    <div class="d-flex align-items-center align-content-center fw-bold">
      <a :href="episode.platform.url" target="_blank">
        <img :src="episode.platform.image" alt="Platform image" class="platform-thumbnail me-2"/>
      </a>
      {{ episode.platform.name }}
    </div>

    <div class="text-start">
      <router-link :to="`/anime/${episode.anime.id}`" class="card-title fw-bold link-color">{{
          episode.anime.name
        }}
      </router-link>

      <p class="card-text">
        <span class="fw-bold">{{ episode.title === null ? "＞﹏＜" : episode.title }}</span>
        <br>
        {{ episode.anime.country.season }} {{ episode.season }} • {{ episode.episodeType[episode.anime.country.tag] }}
        {{ episode.number }} {{ episode.langType[episode.anime.country.tag] }}
        <br>
        <i class="bi bi-camera-reels-fill"></i>
        {{ toHHMMSS(episode.duration) }}
      </p>
    </div>

    <a :href="episode.url" target="_blank">
      <img :src="episode.image" alt="Episode image" class="mb-2 rounded img-fluid w-100 mt-2">
    </a>

    <div class="d-flex">
      <div class="m-0 me-auto justify-content-start">
        Il y a {{ timeSince(episode.releaseDate) }}
      </div>
    </div>
  </div>
</template>
<script>
import Utils from "@/utils";

export default {
  name: 'EpisodeComponent',
  props: {
    episode: {},
  },
  methods: {
    toHHMMSS(duration) {
      return Utils.toHHMMSS(duration.toString())
    },
    timeSince(releaseDate) {
      return Utils.timeSince(new Date(releaseDate).getTime())
    },
  }
}
</script>
<style scoped>
.platform-thumbnail {
  width: 3vh;
  height: 3vh;
  border-radius: 50%;
  margin: .25rem;
}
</style>
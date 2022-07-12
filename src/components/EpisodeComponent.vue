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
import Utils from "@/libs/Utils";

export default {
  name: 'EpisodeComponent',
  extends: Utils,
  props: {
    episode: {},
  }
}
</script>


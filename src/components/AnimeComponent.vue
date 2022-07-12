<template>
  <div class="px-3 py-2 border-color rounded bg-dark">
    <div class="row">
      <div class="col-3 p-2">
        <img :src="getAttachment(anime.image)" alt="Anime image" class="img-fluid rounded" width="640" height="960"/>
      </div>

      <div class="col-9 text-start">
        <div class="text-truncate">
          <router-link :to="`/anime/${anime.url}`" class="link-color">{{ anime.name }}</router-link>
        </div>

        <p class="anime-description">{{ anime.description }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/libs/Utils";

export default {
  name: 'AnimeComponent',
  extends: Utils,
  props: {
    anime: {},
  }
}
</script>
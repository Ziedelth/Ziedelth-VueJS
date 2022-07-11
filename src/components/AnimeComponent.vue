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
import Const from "@/const";

export default {
  name: 'AnimeComponent',
  props: {
    anime: {},
  },
  methods: {
    getAttachment(src) {
      if (src == null || src.startsWith("http")) {
        return src;
      }

      return Const.ATTACHMENTS_URL + src;
    },
    joinGenres() {
      return this.anime.genres.map(genre => genre.fr).join(", ");
    }
  }
}
</script>

<style scoped>
.anime-description {
  display: -webkit-box;
  -webkit-line-clamp: 5;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
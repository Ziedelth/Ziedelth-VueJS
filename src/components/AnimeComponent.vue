<template>
  <div class="p-2 border-color rounded bg-dark mh">
    <div class="row">
      <div class="col-9">
        <router-link :to="`/anime/${anime.id}`" class="link-color">{{ anime.name }}</router-link>
        <p class="anime-description">{{ getAnimeDescription() }}</p>
      </div>

      <div class="col-3 p-2">
        <figure v-lazyload>
          <img :data-url="anime.image" alt="Anime image" class="img-fluid rounded" width="640" height="960"/>
        </figure>
      </div>
    </div>
  </div>
</template>
<script>
import Utils from "@/utils";

export default {
  name: 'AnimeComponent',
  props: {
    anime: {},
  },
  methods: {
    getAnimeDescription() {
      return Utils.isNullOrEmpty(this.anime.description) ? 'Aucune description pour le moment...' : this.anime.description
    },
  }
}
</script>
<style scoped>
.anime-description {
  display: -webkit-box;
  -webkit-line-clamp: 7;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.mh {
  height: 24vh;
  overflow: hidden;
}
</style>
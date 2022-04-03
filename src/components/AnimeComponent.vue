<template>
  <div class="p-2 border-color rounded bg-dark">
    <div class="row mh">
      <div class="col-9">
        <router-link :to="`/anime/${anime.id}`" class="link-color">{{ anime.name }}</router-link>
        <p class="anime-description">{{ getAnimeDescription() }}</p>
      </div>

      <div class="col-3 p-2">
        <figure v-lazyload class="m-0 p-0">
          <img :data-url="anime.image" alt="Anime image" class="img-fluid rounded" width="640" height="960"/>
        </figure>
      </div>
    </div>

    <div class="d-flex mt-1">
      <div v-if="isLogin()" class="m-0 ms-auto justify-content-end">
        <i class="bi bi-hand-thumbs-up-fill me-1" @click="notation(1)" :class="{'text-success': is(1)}" />
        {{ anime.notation }}
        <i class="bi bi-hand-thumbs-down-fill ms-1" @click="notation(-1)" :class="{'text-danger': is(-1)}" />
      </div>
    </div>
  </div>
</template>
<script>
import Utils from "@/utils";
import {mapGetters, mapState} from "vuex";

export default {
  name: 'AnimeComponent',
  props: {
    anime: {},
  },
  computed: {
    ...mapState(['statistics']),
  },
  methods: {
    ...mapGetters(['isLogin']),

    is(count) {
      // If statistics is not defined or empty, return false
      if (!this.statistics || this.statistics.length <= 0) {
        return false;
      }

      return this.statistics.animes.find(stat => stat.anime_id === this.anime.id && stat.count === count) !== undefined;
    },
    notation(count) {
      this.$emit('notation', {anime: this.anime, count: count});
    },
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
  height: 20vh;
  overflow: hidden;
}
</style>
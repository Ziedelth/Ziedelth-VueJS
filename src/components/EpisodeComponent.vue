<template>
  <div class="border-color rounded p-3 bg-dark">
    <div class="d-flex align-items-center align-content-center fw-bold">
      <PlatformComponent :url="episode.platform_url" :image="episode.platform_image" :name="episode.platform" />
    </div>

    <div class="text-start text-truncate">
      <router-link :to="`/anime/${episode.anime_id}`" class="card-title fw-bold link-color">{{
          episode.anime
        }}
      </router-link>

      <p class="card-text">
        <span class="fw-bold">{{ episode.title === null ? "＞﹏＜" : episode.title }}</span>
        <br>
        {{ episode.country_season }} {{ episode.season }} • {{ episode.episode_type }} {{ episode.number }} {{ episode.lang_type }}
        <br>
        <i class="bi bi-camera-reels-fill"></i>
        {{ toHHMMSS(episode.duration) }}
      </p>
    </div>

    <a :href="episode.url" target="_blank">
      <figure v-lazyload class="m-0 p-0">
        <img :data-url="episode.image" alt="Episode image" class="mb-2 rounded img-fluid w-100 mt-2" width="1920" height="1080">
      </figure>
    </a>

    <div class="d-flex">
      <div class="m-0 me-auto justify-content-start">
        Il y a {{ timeSince(episode.release_date) }}
      </div>

      <div v-if="isLogin()" class="m-0 ms-auto justify-content-end">
        <i class="bi bi-hand-thumbs-up-fill me-1" @click="notation(1)" :class="{'text-success': is(1)}" />
        {{ episode.notation }}
        <i class="bi bi-hand-thumbs-down-fill ms-1" @click="notation(-1)" :class="{'text-danger': is(-1)}" />
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapGetters, mapState} from "vuex";

const PlatformComponent = () => import("@/components/PlatformComponent");

export default {
  name: 'EpisodeComponent',
  components: {PlatformComponent},
  props: {
    episode: {},
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

      return this.statistics.episodes.find(stat => stat.episode_id === this.episode.id && stat.count === count) !== undefined;
    },
    notation(count) {
      this.$emit('notation', {episode: this.episode, count: count});
    },
    toHHMMSS(duration) {
      return Utils.toHHMMSS(duration.toString())
    },
    timeSince(releaseDate) {
      return Utils.timeSince(new Date(releaseDate).getTime())
    },
  }
}
</script>


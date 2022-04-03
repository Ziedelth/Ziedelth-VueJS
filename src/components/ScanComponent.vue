<template>
  <div class="border-color rounded p-3 bg-dark">
    <div class="row fw-bold">
      <div class="col-lg-9 text-start">
        <div>
          <PlatformComponent :url="scan.platform_url" :image="scan.platform_image" :name="scan.platform" />
        </div>

        <div class="text-truncate">
          <router-link :to="`/anime/${scan.anime_id}`" class="card-title fw-bold link-color">{{
              scan.anime
            }}
          </router-link>
        </div>

        <p class="card-text">
          {{ scan.episode_type }} {{ scan.number }} {{ scan.lang_type }}
        </p>
      </div>

      <div class="col-lg-3">
        <figure v-lazyload class="m-0 p-0">
          <img :data-url="scan.anime_image" alt="Anime image" class="img-fluid rounded" width="640" height="960"/>
        </figure>
      </div>
    </div>

    <div class="d-flex mt-1">
      <div class="m-0 me-auto justify-content-start">
        Il y a {{ timeSince(scan.release_date) }}
      </div>

      <div v-if="isLogin()" class="m-0 ms-auto justify-content-end">
        <i class="bi bi-hand-thumbs-up-fill me-1" @click="notation(1)" :class="{'text-success': is(1)}" />
        {{ scan.notation }}
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
  name: 'ScanComponent',
  components: {PlatformComponent},
  props: {
    scan: {},
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

      return this.statistics.scans.find(stat => stat.scan_id === this.scan.id && stat.count === count) !== undefined;
    },
    toHHMMSS(duration) {
      return Utils.toHHMMSS(duration.toString())
    },
    timeSince(releaseDate) {
      return Utils.timeSince(new Date(releaseDate).getTime())
    },
  notation(count) {
    this.$emit('notation', {scan: this.scan, count: count});
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
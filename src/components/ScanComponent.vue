<template>
  <div class="border-color rounded p-3 bg-dark">
    <div class="row fw-bold">
      <div class="col-lg-9 text-start">
        <div>
          <a :href="scan.platform.url" target="_blank">
            <img :src="scan.platform.image" alt="Platform image" class="platform-thumbnail me-2"/>
          </a>

          {{ scan.platform.name }}
        </div>

        <div class="text-truncate">
          <router-link :to="`/anime/${scan.anime.id}`" class="card-title fw-bold link-color text-truncate">{{
              scan.anime.name
            }}
          </router-link>
        </div>

        <p class="card-text">
          {{ scan.episodeType[scan.anime.country.tag] }} {{ scan.number }} {{ scan.langType[scan.anime.country.tag] }}
        </p>

        <div class="d-flex">
          <div class="m-0 me-auto justify-content-start">
            Il y a {{ timeSince(scan.releaseDate) }}
          </div>
        </div>
      </div>

      <div class="col-lg-3">
        <img :src="scan.anime.image" alt="Anime image" class="img-fluid rounded"/>
      </div>
    </div>
  </div>
</template>
<script>
import Utils from "@/utils";

export default {
  name: 'ScanComponent',
  props: {
    scan: {},
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
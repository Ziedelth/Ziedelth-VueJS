<template>
  <div class="border-color rounded p-3 bg-dark">
    <div class="row fw-bold">
      <div class="col-lg-9 text-start">
        <div>
          <PlatformComponent :url="scan.platform_url" :image="scan.platform_image" :name="scan.platform" />
        </div>

        <AnimeComponent :id="scan.anime_id" :name="scan.anime" />

        <p class="card-text">
          {{ scan.episode_type }} {{ scan.number }} {{ scan.lang_type }}
        </p>

        <div class="d-flex">
          <div class="m-0 me-auto justify-content-start">
            Il y a {{ timeSince(scan.release_date) }}
          </div>
        </div>
      </div>

      <div class="col-lg-3">
        <img :src="scan.anime_image" alt="Anime image" class="img-fluid rounded"/>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";
import PlatformComponent from "@/components/PlatformComponent";
import AnimeComponent from "@/components/AnimeComponent";

export default {
  name: 'ScanComponent',
  components: {PlatformComponent, AnimeComponent},
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
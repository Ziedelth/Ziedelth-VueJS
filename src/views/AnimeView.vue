<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <div class="container-fluid">
        <div class="container mb-3">
          <div class="row g-3">
            <div class="col-lg-3 text-lg-end">
              <img :src="getAttachment(anime.image)" alt="Anime image" class="rounded" style="width: auto; height: 20vh">
            </div>

            <div class="col-lg-9 text-lg-start">
              <h3>{{ anime.name }}</h3>
              <p>{{ joinGenres() }}</p>
              <hr>
              <p>{{ anime.description }}</p>
            </div>
          </div>
        </div>

        <Episodes :episodes="episodes" />
      </div>
    </div>
  </div>
</template>

<script>
import Const from "@/const";
import Utils from "@/libs/Utils";
import MyBrotli from "@/libs/my_brotli";
import LoadingComponent from "@/components/LoadingComponent";
import Episodes from "@/components/Episodes";

export default {
  components: {LoadingComponent, Episodes},
  extends: Utils,
  data() {
    return {
      page: 1,
      isLoading: true,
      anime: {},
      episodes: []
    }
  },
  methods: {
    async loadEpisodes() {
      try {
        const response = await fetch(`${Const.API_URL}v2/animes/${this.$route.params.id}/episodes/page/${this.page}/limit/12`)
        const data = await response.text()
        this.episodes.push(...JSON.parse(MyBrotli(data)))

        if (this.episodes.length > 0) {
          this.anime = this.episodes[0].anime
        }
      } catch (e) {
        console.error(e)
      }
    }
  },
  async created() {
    this.isLoading = true
    await this.loadEpisodes()
    this.isLoading = false

    window.onscroll = () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

      if (bottomOfWindow) {
        this.page++
        this.loadEpisodes()
      }
    };
  }
}
</script>
<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <div class="row row-cols-lg-4 g-3">
        <div v-for="simulcast in simulcasts" :key="simulcast.id" class="my-3" @click="changeSimulcast(simulcast)">
          <div class="m-1 border-color rounded" :class="{'bg-dark': simulcast.id === currentSimulcast.id, 'fw-bold': simulcast.id === currentSimulcast.id}">
            {{ simulcast.simulcast }}
          </div>
        </div>
      </div>

      <div class="row row-cols-lg-4 g-3 d-flex justify-content-center text-center">
        <div v-for="anime in animes" class="col-lg">
          <AnimeComponent :anime="anime" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Const from "@/const";
import MyBrotli from "@/libs/my_brotli";
import LoadingComponent from "@/components/LoadingComponent";
import AnimeComponent from "@/components/AnimeComponent";

export default {
  components: {AnimeComponent, LoadingComponent},
  data() {
    return {
      page: 1,
      isLoading: true,
      simulcasts: [],
      currentSimulcast: null,
      animes: [],
    }
  },
  methods: {
    async loadAnimes() {
      const response = await fetch(`${Const.API_URL}v2/animes/country/fr/simulcast/${this.currentSimulcast.id}/page/${this.page}/limit/24`)
      const data = await response.text()
      this.animes.push(...JSON.parse(MyBrotli(data)))
    },
    async changeSimulcast(simulcast) {
      this.currentSimulcast = simulcast
      this.page = 1
      this.animes = []
      this.isLoading = true
      await this.loadAnimes()
      this.isLoading = false
    },
    async load() {
      try {
        let response = await fetch(`${Const.API_URL}v2/simulcasts`)
        let data = await response.text()
        this.simulcasts = JSON.parse(MyBrotli(data))
        await this.changeSimulcast(this.simulcasts[this.simulcasts.length - 1])
      } catch (e) {
        console.error(e)
      }
    }
  },
  async created() {
    this.isLoading = true
    await this.load()
    this.isLoading = false

    window.onscroll = () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

      if (bottomOfWindow) {
        this.page++
        this.loadAnimes()
      }
    };
  }
}
</script>


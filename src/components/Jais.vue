<template>
  <div id="jais" class="text-center">
    <div class="row g-3">
      <div class="col-lg-4" />

      <div class="col-lg-4">
        <img alt="Jaïs brand" class="m-0 p-0 img-fluid collapsed border-color rounded-circle shadow" src="images/jais.jpg" width="120" height="120"/>

        <div class="container-fluid">
          <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
          <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>

          <div class="d-inline w-100">
            <a class="link-color" href="https://twitter.com/Jaiss___" target="_blank"><img alt="Twitter" height="20" src="../assets/svg/twitter.svg" class="mx-2" width="20"></a>
            <a class="link-color" href="https://www.instagram.com/jais_zie/" target="_blank"><img alt="Instagram" height="20" src="../assets/svg/instagram.svg" class="mx-2" width="20"></a>
            <a class="link-color" href="https://github.com/Ziedelth/Jais" target="_blank"><img alt="GitHub" height="20" src="../assets/svg/github.svg" class="mx-2" width="20"></a>
            <a class="link-color" href="#"><img alt="Discord" height="20" src="../assets/svg/discord.svg" class="mx-2" width="20"></a>
          </div>
        </div>
      </div>

      <div class="col-lg-4 mt-0">
        <a href='https://play.google.com/store/apps/details?id=fr.ziedelth.jais&gl=FR&pcampaignid=pcampaignidMKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1' target="_blank"><img alt='Disponible sur Google Play' src='images/google_play.png' style="width: 15rem" /></a>
        <br>
        <a href='https://ziedelth.fr/attachments/jais.apk' target="_blank"><img alt='Disponible sur Google Play' src='images/apk.png' style="width: 13rem" /></a>
      </div>
    </div>

    <LoadingComponent :is-loading="isLoading" />

    <div v-if="!isLoading">
      <div class="mb-3">
        <p class="text mt-0 muted fw-bold">Les derniers épisodes sortis</p>
        <Episodes :episodes="episodes" />
      </div>
    </div>
  </div>
</template>

<script>
import Const from "@/const";
import MyBrotli from "@/libs/my_brotli";

const LoadingComponent = () => import("@/components/LoadingComponent");
const Episodes = () => import("@/components/Episodes");

export default {
  name: "Jais",
  components: {LoadingComponent, Episodes},
  data() {
    return {
      page: 1,
      isLoading: true,
      episodes: [],
    }
  },
  methods: {
    async load() {
      try {
        const response = await fetch(`${Const.API_URL}v2/episodes/country/fr/page/${this.page}/limit/12`)
        const data = await response.text()
        this.episodes.push(...JSON.parse(MyBrotli(data)))
      } catch (e) {
        console.error(e)
      }
    },
  },
  async created() {
    this.isLoading = true
    await this.load()
    this.isLoading = false

    window.onscroll = () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

      if (bottomOfWindow) {
        this.page++
        this.load()
      }
    };
  }
}
</script>


<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div class="container mb-3">
        <input v-model="filter" class="form-control" placeholder="Recherchez un anime..." type="text">
      </div>

      <div v-if="error === null" class="row g-3 row-cols-lg-3 row-cols-1">
        <div v-for="anime in filtered" class="col">
          <div class="p-2 border-color rounded bg-dark mh">
            <div class="row">
              <div class="col-9">
                <router-link :to="`/anime/${anime.id}`" class="link-color">{{ anime.name }}</router-link>
                <p class="anime-description">{{ getAnimeDescription(anime) }}</p>
              </div>

              <div class="col-3 p-2">
                <img :src="anime.image" alt="Anime image" class="img-fluid rounded"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Utils from "@/utils";

const LoadingComponent = () => import("@/components/LoadingComponent");

export default {
  components: {LoadingComponent},
  data() {
    return {
      isLoading: true,
      error: null,
      animes: [],
      filter: '',
      filtered: [],
    }
  },
  methods: {
    getAnimeDescription(anime) {
      return Utils.isNullOrEmpty(anime.description) ? 'Aucune description pour le moment...' : anime.description
    },
  },
  watch: {
    filter: function (val) {
      if (Utils.isNullOrEmpty(val))
        this.filtered = Object.assign({}, this.animes)
      else
        this.filtered = this.animes.filter(value => value.name.toLowerCase().includes(val.toLowerCase()))
    }
  },
  async mounted() {
    this.isLoading = true

    await Utils.get(`php/v1/jais/animes.php`, 200, (animes) => {
      this.animes = this.filtered = animes
    }, (failed) => {
      this.error = `${failed}`
    })

    this.isLoading = false
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
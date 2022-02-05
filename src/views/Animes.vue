<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div class="container w-25 mb-3">
        <input type="text" class="form-control" placeholder="Recherchez un anime..." v-model="filter">
      </div>

      <div v-if="error === null" class="row g-3">
        <div v-for="anime in filtered" class="col-lg-4">
          <div class="p-2 border-color rounded bg-dark">
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
      return (anime.description === null || anime.description.length <= 0) ? 'Aucune description pour le moment...' : anime.description;
    },
  },
  watch: {
    filter: function (val) {
      if (val == null || val.length <= 0)
        this.filtered = Object.assign({}, this.animes)
      else
        this.filtered = this.animes.filter(value => value.name.toLowerCase().includes(val.toLowerCase()))
    }
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/v1/animes.php"))

      if (response.status === 200) {
        this.animes = this.filtered = await response.json();
        this.error = null;
      } else {
        this.animes = this.filtered = [];
        this.error = response.statusText;
      }
    } catch (exception) {
      this.animes = this.filtered = [];
      this.error = exception;
    }

    this.isLoading = false;
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
</style>
<template>
  <div>
    <div v-if="isLoading">
      <div class="spinner-border my-2" role="status"/>
      <p class="fw-bold">Chargement...</p>
    </div>

    <div v-else>
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else class="row g-3">
        <div v-for="anime in animes" class="col-lg-4">
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

export default {
  data() {
    return {
      isLoading: true,
      error: null,
      animes: [],
    }
  },
  methods: {
    getAnimeDescription(anime) {
      return (anime.description === null || anime.description.length <= 0) ? 'Aucune description pour le moment...' : anime.description;
    },
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/v1/animes.php"))

      if (response.status === 201) {
        this.animes = await response.json();
        this.error = null;
      } else {
        this.animes = [];
        this.error = response.statusText;
      }
    } catch (exception) {
      this.animes = [];
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
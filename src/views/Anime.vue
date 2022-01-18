<template>
  <div>
    <div v-if="isLoading">
      <div class="spinner-border my-2" role="status"/>
      <p class="fw-bold">Chargement...</p>
    </div>

    <div v-else>
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else class="container">
        <div>
          <img :src="anime.image" alt="Anime image" class="img-fluid rounded mb-3">
          <h3>{{ anime.name }}</h3>
          <p>{{ anime.genres.map((e) => e.fr).join(' - ') }}</p>
          <hr>
          <p class="text-muted">{{ getAnimeDescription() }}</p>
        </div>

        <div v-if="anime.seasons.length > 0" class="text-start">
          <div class="row g-3">
            <div class="col-lg-3 bg-dark p-3">
              <ul>
                <li v-for="season in anime.seasons">
                  {{ anime.country.season }} {{ season.season }}

                  <ul>
                    <li v-for="episode in season.episodes">
                      {{ episode.resume }}
                    </li>
                  </ul>
                </li>
              </ul>
            </div>

            <div class="col-lg-9">

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
      id: this.$route.params.id,
      isLoading: true,
      error: null,
      anime: [],
    }
  },
  methods: {
    getAnimeDescription() {
      return (this.anime.description === null || this.anime.description.length <= 0) ? 'Aucune description pour le moment...' : this.anime.description;
    }
  },
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/v1/get_anime.php?id=" + this.id))

      if (response.status === 201) {
        this.anime = await response.json();
        this.error = null;
      } else {
        this.anime = [];
        this.error = response.statusText;
      }
    } catch (exception) {
      this.anime = [];
      this.error = exception;
    }

    this.isLoading = false;
  }
}
</script>
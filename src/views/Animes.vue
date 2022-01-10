<template>
  <div class="text-center">
    <div v-if="isLoading" class="spinner-border my-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>

    <div v-else class="row g-3">
      <div class="col-lg-12">
        <div class="container-fluid">
          <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

          <div v-else class="container-fluid row g-3">
            <div v-for="anime in animes" class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-1">
                      <img :src="anime.image" alt="Anime image" class="img-fluid"/>
                    </div>

                    <div class="col-11">
                      <div class="d-flex align-items-center align-content-center fw-bold">
                        <a v-for="platform in anime.platforms" :href="platform.url" target="_blank">
                          <img :src="platform.image" alt="Platform image" class="platform-thumbnail me-2"/>
                        </a>
                      </div>

                      <div class="text-start">
                        <p class="card-title fw-bold mb-0">{{ anime.name }}</p>
                        <p v-if="anime.episodes > 0" class="mb-0 text-muted">Épisodes : {{ anime.episodes }}</p>
                        <p v-if="anime.scans > 0" class="mb-0 text-muted">Scans : {{ anime.scans }}</p>
                        <p>{{ anime.genres.join(', ') }}</p>
                        <hr>
                        <p class="text-muted">{{ anime.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
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
  async mounted() {
    this.isLoading = true;

    try {
      const response = await fetch(Utils.getLocalFile("php/jais/animes.php?country=fr"))

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
.platform-thumbnail {
  width: 3vh;
  height: 3vh;
  border-radius: 50%;
  margin: .25rem;
}
</style>
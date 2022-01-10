<template>
  <div id="jais" class="text-center">
    <img alt="Jaïs brand" class="img-fluid collapsed border rounded-circle shadow" height="120"
         loading="lazy" src="images/jais.jpg" width="120"/>

    <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
    <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>
    <p class="text mt-0 muted fw-bold">Les dernières sorties</p>

    <div v-if="isLoading" class="spinner-border my-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>

    <div v-else class="row g-3">
      <div class="col-lg-12">
        <div class="container-fluid">
          <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

          <div v-else class="container-fluid row g-3">
            <div v-for="episode in episodes" class="col-lg-3">
              <div class="card">
                <div class="card-body">
                  <div class="text-truncate">
                    <div class="d-flex align-items-center align-content-center fw-bold">
                      <a :href="episode.platform_url" target="_blank">
                        <img :src="episode.platform_image" alt="Platform image" class="platform-thumbnail me-2"/>
                      </a>
                      {{ episode.platform }}
                    </div>

                    <div class="text-start">
                      <p class="card-title fw-bold mb-1">{{ episode.anime }}</p>

                      <p class="card-text">
                        <span class="fw-bold">{{ episode.title === null ? "＞﹏＜" : episode.title }}</span>
                        <br>
                        {{ episode.resume }}
                        <br>
                        <i class="bi bi-camera-reels-fill"></i>
                        {{ toHHMMSS(episode.duration) }}
                      </p>
                    </div>
                  </div>

                  <a :href="episode.episode_url" target="_blank">
                    <img :src="episode.episode_image" alt="Episode image" class="mb-2 rounded img-fluid w-100 mt-2">
                  </a>

                  <div class="d-flex">
                    <div class="m-0 me-auto justify-content-start">
                      Il y a {{ timeSince(episode.release_date) }}
                    </div>

                    <div class="m-0 ms-auto justify-content-end">
                      <div class="d-inline me-2">
                        <i class="bi bi-check2"></i>
                        {{ episode.checks.length }}
                      </div>

                      <div class="d-inline ">
                        <i class="bi bi-heart-fill"></i>
                        {{ episode.loves.length }}
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
import Utils from "../utils";

export default {
  name: "Jais",
  data() {
    return {
      isLoading: true,
      limit: 12,
      error: null,
      episodes: [],
    }
  },
  methods: {
    toHHMMSS(duration) {
      return Utils.toHHMMSS(duration.toString())
    },
    timeSince(releaseDate) {
      return Utils.timeSince(new Date(releaseDate).getTime())
    },
    async getEpisodes(add = false) {
      if (add)
        this.limit += 12;

      try {
        const response = await fetch(Utils.getLocalFile("php/jais/latest_episodes.php?country=fr&limit=" + this.limit))

        if (response.status === 201) {
          this.episodes = await response.json();
          this.error = null;
        } else {
          this.episodes = [];
          this.error = response.statusText;
        }
      } catch (exception) {
        this.episodes = [];
        this.error = exception;
      }

      this.isLoading = false;
    }
  },
  async mounted() {
    this.isLoading = true;
    await this.getEpisodes();
    setInterval(async () => await this.getEpisodes(), 60 * 1000);

    window.onscroll = async () => {
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;
      if (bottomOfWindow && !this.isLoading) {
        await this.getEpisodes(true)
      }
    }
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
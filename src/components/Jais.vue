<template>
  <div id="jais" class="text-center">
    <img v-b-toggle.jais alt="Jaïs brand" class="img-fluid collapsed border rounded-circle shadow" height="120"
         loading="lazy" src="images/jais.jpg" width="120" @click="imgClick"/>

    <div id="indicator" class="fw-bold my-3">
      <b-icon-arrow-up-square-fill font-scale="1.25"/>
    </div>

    <b-collapse id="jais" visible>
      <h2 class="mt-2 mb-0 fw-bold">Jaïs</h2>
      <p class="lead mt-0 mb-0 fw-bold">Un bot fana d'animés et de mangas</p>
      <p class="text mt-0 muted fw-bold">Les dernières sorties</p>

      <b-container fluid>
        <b-icon v-show="isLoading" animation="cylon" class="my-2" font-scale="2" icon="three-dots"></b-icon>

        <div v-show="!isLoading">
          <p v-show="error !== null" class="alert-danger text-danger">{{ error }}</p>

          <b-container class="row g-3" fluid>
            <div v-for="episode in episodes" class="col-lg-4">
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
                        Saison {{ episode.season }} • {{
                          episode.episode_type === "EPISODE" ? "Épisode" : episode.episode_type === "SPECIAL" ? "Spécial" : "Film"
                        }} {{ episode.number }} {{ episode.lang_type === "SUBTITLES" ? "VOSTFR" : "VF" }}
                        <br>
                        <b-icon-camera-reels-fill/>
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
                        <b-icon-check2/>
                        0
                      </div>

                      <div class="d-inline ">
                        <b-icon-heart-fill/>
                        0
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </b-container>
        </div>
      </b-container>
    </b-collapse>
  </div>
</template>

<script>
import Utils from "../utils";

export default {
  name: "Jais",
  data() {
    return {
      show: true,

      isLoading: true,
      error: null,
      episodes: [],
    }
  },
  methods: {
    imgClick() {
      this.show = !this.show;

      document.getElementById("indicator").classList.toggle('active');
    },
    toHHMMSS(duration) {
      if (duration <= 0) {
        return '??:??';
      }

      const sec_num = parseInt(duration.toString(), 10); // don't forget the second param
      let hours = Math.floor(sec_num / 3600);
      hours = hours < 10 ? '0' + hours : hours;
      let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
      minutes = minutes < 10 ? '0' + minutes : minutes;
      let seconds = sec_num - (hours * 3600) - (minutes * 60);
      seconds = seconds < 10 ? '0' + seconds : seconds;
      return (hours >= 1 ? hours + ':' : '') + minutes + ':' + seconds;
    },
    getDateTime(releaseDate) {
      return new Date(releaseDate).getTime()
    },
    timeSince(releaseDate) {
      const seconds = Math.floor((new Date() - this.getDateTime(releaseDate)) / 1000);
      let interval = seconds / 31536000;
      if (interval > 1) return Math.floor(interval) + " an" + (interval >= 2 ? "s" : "");
      interval = seconds / 2592000;
      if (interval > 1) return Math.floor(interval) + " mois";
      interval = seconds / 86400;
      if (interval > 1) return Math.floor(interval) + " jour" + (interval >= 2 ? "s" : "");
      interval = seconds / 3600;
      if (interval > 1) return Math.floor(interval) + " heure" + (interval >= 2 ? "s" : "");
      interval = seconds / 60;
      if (interval > 1) return Math.floor(interval) + " minute" + (interval >= 2 ? "s" : "");
      return Math.floor(seconds) + " seconde" + (seconds >= 2 ? "s" : "");
    },
    getEpisodes() {
      if (this.show) {
        setTimeout(() => {
          fetch(Utils.getLocalFile("php/jais/latest_episodes.php"))
              .then(response => response.json())
              .then(response => {
                this.isLoading = false;
                this.episodes = response;
                this.error = null;
              })
              .catch(error => {
                this.isLoading = false;
                this.episodes = [];
                this.error = error;
              });
        }, 1000);
      }
    }
  },
  mounted() {
    this.isLoading = true;
    this.getEpisodes();
    setInterval(() => this.getEpisodes(), 60 * 1000);
  }
}
</script>

<style scoped>
#indicator {
  -webkit-transition-duration: 0.5s;
  -moz-transition-duration: 0.5s;
  -o-transition-duration: 0.5s;
  transition-duration: 0.5s;
  -webkit-transition-property: -webkit-transform;
  -moz-transition-property: -moz-transform;
  -o-transition-property: -o-transform;
  transition-property: transform;
  outline: 0;
}

.active {
  -webkit-transform: rotate(180deg);
  -moz-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  -o-transform: rotate(180deg);
  transform: rotate(180deg);
}

.platform-thumbnail {
  width: 3vh;
  height: 3vh;
  border-radius: 50%;
  margin: .25rem;
}
</style>
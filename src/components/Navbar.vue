<template>
  <header>
    <b-navbar class="px-4" toggleable="lg" type="dark" variant="dark">
      <b-container fluid>
        <router-link class="navbar-brand" to="/">
          <img alt="" class="d-inline-block align-text-top me-2 rounded" height="30" loading="lazy"
               src="images/favicon.jpg" width="30">
          Ziedelth.fr
        </router-link>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
          <b-navbar-nav class="me-auto my-2 my-lg-0 navbar-nav-scroll">
            <router-link class="nav-link" to="/">Accueil</router-link>
          </b-navbar-nav>

          <b-navbar-nav class="ml-auto">
            <b-button v-b-modal.modal class="my-2 my-sm-0" variant="success">
              <b-icon-search/>
            </b-button>

            <b-modal id="modal" centered hide-footer ok-only scrollable size="lg">
              <template #modal-header="{ close }">
                <h5>Rechercher un anime</h5>

                <!-- Emulate built in modal header close button action -->
                <b-icon-x @click="close()"/>
              </template>

              <b-form-input id="input-horizontal" v-model="search" placeholder="Entrez le nom d'un anime"
                            @change="onSearch"></b-form-input>
              <hr>
              <b-container class="text-center" fluid>
                <b-icon v-show="isLoading" animation="cylon" class="my-2" font-scale="2" icon="three-dots"></b-icon>

                <div v-show="!isLoading">
                  <p v-show="error !== null" class="alert-danger text-danger">{{ error }}</p>

                  <div class="row g-3 text-start">
                    <div v-for="anime in animes" class="">
                      <div class="col-12 border rounded">
                        <div class="row p-2">
                          <div class="col-md-3">
                            <img :src="anime.image" alt="Anime image" class="img-fluid rounded position-relative">
                          </div>

                          <div class="col-md-9">
                            <div class="d-flex">
                              <div class="mb-0">
                                <div class="d-flex align-items-center align-content-center">
                                  <img :src="anime.platform.image" alt="Platform image"
                                       class="platform-thumbnail me-2"/>

                                  <p class="fw-bold mb-0">
                                    {{ anime.name }}
                                  </p>
                                </div>

                                <p class="text-muted mb-0">Épisode{{ anime.episodes > 1 ? "s" : "" }} :
                                  {{ anime.episodes }} (
                                  <b-icon-camera-reels-fill/>
                                  {{ anime.duration <= 0 ? "??:??" : toHHMMSS(anime.duration) }})
                                </p>
                                <p>{{ anime.genres.map(genre => genre.genre).join(', ') }}</p>
                              </div>
                            </div>

                            <p class="text-muted">{{ anime.description === null ? "＞︿＜" : anime.description }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </b-container>
            </b-modal>
          </b-navbar-nav>
        </b-collapse>
      </b-container>
    </b-navbar>
  </header>
</template>

<script>
import Utils from "@/utils";

export default {
  data() {
    return {
      search: null,
      isLoading: false,
      error: null,
      animes: [],
    }
  },
  methods: {
    toHHMMSS(duration) {
      const sec_num = parseInt(duration.toString(), 10); // don't forget the second param
      let hours = Math.floor(sec_num / 3600);
      let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
      let seconds = sec_num - (hours * 3600) - (minutes * 60);
      if (hours < 10) {
        hours = "0" + hours;
      }
      if (minutes < 10) {
        minutes = "0" + minutes;
      }
      if (seconds < 10) {
        seconds = "0" + seconds;
      }
      return (hours >= 1 ? hours + ':' : '') + minutes + ':' + seconds;
    },

    onSearch() {
      this.isLoading = true;
      this.animes = [];
      this.error = null;

      setTimeout(() => {
        fetch(Utils.getLocalFile("php/search.php?text=" + this.search))
            .then(response => response.json())
            .then(response => {
              this.isLoading = false;
              this.animes = response;
              this.error = null;
            })
            .catch(error => {
              this.isLoading = false;
              this.animes = [];
              this.error = error;
            });
      }, 1000);
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
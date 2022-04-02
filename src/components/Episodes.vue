<template>
  <div class="row row-cols-lg-4 g-3 d-flex justify-content-center text-center mb-3">
    <div v-for="episode in episodes" class="col" :key="episode.episode_id" @dblclick="showEpisode(episode)">
      <EpisodeComponent :episode="episode"/>
    </div>

    <b-modal id="modal-episode" title="Édition de l'épisode" size="xl" hide-header-close hide-footer centered scrollable>
      <div class="row row-cols-lg-2 g-3 d-flex justify-content-center mb-3">
        <div class="col">
          <div class="form-group">
            <label for="episode-release-date">Date de sortie</label>
            <input type="text" class="form-control" id="episode-release-date" v-model="currentEpisode.release_date">
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <label for="episode-season">Saison</label>
            <input type="number" class="form-control" id="episode-season" v-model="currentEpisode.season">
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <label for="episode-number">Numéro</label>
            <input type="number" class="form-control" id="episode-number" v-model="currentEpisode.number">
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <label for="episode-title">Titre</label>
            <input type="text" class="form-control" id="episode-title" v-model="currentEpisode.title">
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <label for="episode-url">URL</label>
            <input type="text" class="form-control" id="episode-url" v-model="currentEpisode.url">
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <label for="episode-duration">Durée</label>
            <input type="number" class="form-control" id="episode-duration" v-model="currentEpisode.duration">
          </div>
        </div>
      </div>

      <div class="w-100 text-center">
        <button class="btn btn-primary" @click="saveEpisode($event)">Enregistrer</button>
      </div>
    </b-modal>
  </div>
</template>

<script>
import {mapState} from "vuex";
import Utils from "@/utils";

const EpisodeComponent = () => import("@/components/EpisodeComponent");

export default {
  name: 'Episodes',
  components: {
    EpisodeComponent
  },
  props: {
    episodes: {}
  },
  data() {
    return {
      currentEpisode: {}
    }
  },
  computed: {
    ...mapState(['token', 'user']),
  },
  methods: {
    showEpisode(episode) {
      // If user is null or role not equals to 100, return
      if (!this.user || this.user.role !== 100) return;

      this.currentEpisode = Object.assign({}, episode);
      this.$bvModal.show('modal-episode');
    },
    async saveEpisode(event) {
      // If user is null or role not equals to 100, return
      if (!this.user || this.user.role !== 100) return;

      event.disabled = true;

      this.currentEpisode.token = this.token;

      await Utils.put(`api/v1/episode/update`, JSON.stringify(this.currentEpisode), (success) => {
        event.disabled = false;

        if (!("success" in success))
          return

        this.$bvModal.hide('modal-episode');
        // Refresh episodes
        this.$emit('refresh');
      }, (failed) => null)
    }
  }
}
</script>
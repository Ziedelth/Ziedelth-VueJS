<template>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">

        <!-- HEADER -->
        <div class="row">
          <div class="col-9 text-truncate">
            <div class="d-flex align-items-center align-content-center">
              <a :href="episode.platform.url"><img :alt="episode.platform.name" :src="getPlatformImage"
                                                   class="platform-thumbnail me-2"></a>{{
                episode.platform.name
              }}
            </div>

            <div class="text-start">
              <p class="card-title fw-bold mb-1">{{ episode.anime.name }}</p>

              <p class="card-text">
                {{ (episode.title || null) === null ? "¯\\_(ツ)_/¯" : episode.title }}
                <br>

                {{ episode.country.season }} {{ episode.season }} • {{ getEpisodeType }} {{ getEpisodeNumber }}
                <br>
                <b-icon-camera-reels-fill/>
                {{ toHHMMSS }}
              </p>
            </div>
          </div>

          <div class="col-3 text-end">
            <img :src="getAnimeImage" alt="Anime brand" class="img-fluid border rounded">
          </div>
        </div>

        <!-- BODY -->
        <a v-if="!isMultipleEpisode" :href="episode.url" target="_blank"><img :src="episode.image"
                                                                              alt="Anime image episode"
                                                                              class="mb-2 rounded img-fluid w-100 mt-2 border"></a>

        <div v-else class="position-relative">
          <span
              class="position-absolute top-0 start-100 translate-middle-x badge border border-light rounded-circle bg-danger p-2"><span
              class="visually-hidden">Multiple episodes</span></span>
          <img :src="episode.image" alt="Anime image episode" class="mb-2 rounded img-fluid w-100 mt-2 border">
        </div>

        <!-- FOOTER -->
        <div class="d-flex">
          <div class="m-0 me-auto justify-content-start">
            <img :src="getJaisImage" alt="Jais" class="img-fluid rounded-circle me-1" style="width: 1.5rem">
            {{ episode.langType === "VOICE" ? episode.country.dubbed : episode.country.subtitles }} • Il y a
            {{ timeSince }}
          </div>

          <div v-if="!isMultipleEpisode">
            <div class="m-0 ms-auto justify-content-end">
              <div class="d-inline me-2 pointer" @click="claimEpisode">
                <span v-show="hasClaimEpisode" class="claim"><b-icon-check2/> {{ episode.claims || 0 }}</span>
                <span v-show="!hasClaimEpisode"><b-icon-check2/> {{ episode.claims || 0 }}</span>
              </div>

              <div class="d-inline pointer" @click="loveEpisode">
                <span v-show="hasLoveEpisode" class="love"><b-icon-heart-fill/> {{ episode.loves || 0 }}</span>
                <span v-show="!hasLoveEpisode"><b-icon-heart-fill/> {{ episode.loves || 0 }}</span>
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
import {mapActions, mapGetters, mapState} from "vuex";

export default {
  props: {
    episode: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapState(["limit"]),
    ...mapGetters(["getFavicon", "getJaisImage"]),

    isMultipleEpisode() {
      return this.episode.multiple === true;
    },

    getPlatformImage() {
      return Utils.getFile(this.episode.platform.image)
    },

    getAnimeImage() {
      return Utils.isNull(this.episode.anime.image) ? this.getFavicon : this.episode.anime.image
    },

    getEpisodeType() {
      switch (this.episode.episodeType) {
        case 'FILM':
          return this.episode.country.film
        case 'SPECIAL':
          return this.episode.country.special
        default:
          return this.episode.country.episode
      }
    },

    getEpisodeNumber() {
      return this.isMultipleEpisode ? (this.episode.number.split(",")[0] + " → " + this.episode.number.split(",")[1]) : this.episode.number
    },

    toHHMMSS() {
      return Utils.toHHMMSS(this.episode.duration.toString())
    },

    timeSince() {
      return Utils.timeSince(new Date(this.episode.releaseDate).getTime())
    },

    hasClaimEpisode() {
      return false
    },

    hasLoveEpisode() {
      return false
    }
  },
  methods: {
    ...mapActions(["getData"]),

    fetchEpisodes(add = 0) {
      this.getData(this.limit + add)
    },

    async claimEpisode() {

    },

    async loveEpisode() {

    }
  }
}
</script>

<style scoped>
.platform-thumbnail {
  width: 2rem;
  border-radius: 50%;
  margin: .25rem;
}

.pointer {
  cursor: pointer;
}

.claim {
  color: #27ae60;
}

.love {
  color: #c0392b;
}
</style>
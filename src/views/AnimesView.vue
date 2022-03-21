<template>
  <div>
    <LoadingComponent :is-loading="isLoading"/>

    <div v-if="!isLoading">
      <p v-if="error !== null" class="alert-danger text-danger">{{ error }}</p>

      <div v-else>
        <div class="container mb-3">
          <input v-model="search" class="form-control" placeholder="Recherchez un anime..." type="text">

          <div class="mt-2">
            <b-icon-funnel-fill class="me-2" scale="1.5" />
            <button :class="{'active': filter === 'asc_name'}" class="btn btn-outline-secondary mx-1" @click="filter = 'asc_name'"><b-icon-sort-alpha-down /></button>
            <button :class="{'active': filter === 'desc_name'}" class="btn btn-outline-secondary mx-1" @click="filter = 'desc_name'"><b-icon-sort-alpha-up /></button>
          </div>
        </div>

        <div class="row g-3">
          <div v-for="anime in getItems" class="col-lg-3">
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
  </div>
</template>

<script>
import Utils from "@/utils";
import {mapState} from "vuex";

const LoadingComponent = () => import("@/components/LoadingComponent");

export default {
  components: {LoadingComponent},
  computed: {
    ...mapState(['user', 'currentCountry']),

    getItems() {
      return this.searchItems.sort((a, b) => this.filter === 'asc_name' ? a.name.toLowerCase().localeCompare(b.name.toLowerCase()) : b.name.toLowerCase().localeCompare(a.name.toLowerCase()))
    },
  },
  data() {
    return {
      filter: 'asc_name',

      search: '',
      searchItems: [],

      isLoading: true,
      animes: [],
      error: null,
    }
  },
  methods: {
    getAnimeDescription(anime) {
      return Utils.isNullOrEmpty(anime.description) ? 'Aucune description pour le moment...' : anime.description
    },
  },
  watch: {
    search: function (val) {
      this.searchItems = Utils.isNullOrEmpty(val) ? Object.assign({}, this.animes) : this.animes.filter(value => value.name.toLowerCase().includes(val.toLowerCase()))
    }
  },
  async mounted() {
    this.isLoading = true

    await Utils.get(`api/v1/country/${this.currentCountry.tag}/animes`, (animes) => {
      this.animes = this.searchItems = animes
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
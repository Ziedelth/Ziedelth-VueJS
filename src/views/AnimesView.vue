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
            <button :class="{'active': filter === 'asc_time'}" class="btn btn-outline-secondary mx-1" @click="filter = 'asc_time'"><b-icon-calendar-plus /></button>
            <button :class="{'active': filter === 'desc_time'}" class="btn btn-outline-secondary mx-1" @click="filter = 'desc_time'"><b-icon-calendar-minus /></button>
          </div>
        </div>

        <div class="row row-cols-lg-4 g-3 d-flex justify-content-center text-center mb-3">
          <div v-for="anime in getItems" class="col-lg">
            <AnimeComponent :anime="anime" />
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
const AnimeComponent = () => import("@/components/AnimeComponent");

export default {
  components: {AnimeComponent, LoadingComponent},
  computed: {
    ...mapState(['user', 'currentCountry']),

    getItems() {
      return this.searchItems.sort((a, b) => {
        switch (this.filter) {
          case "asc_name":
            return a.name.toLowerCase().localeCompare(b.name.toLowerCase())
          case "desc_name":
            return b.name.toLowerCase().localeCompare(a.name.toLowerCase())
          case "asc_time":
            return new Date(b.release_date).getTime() - new Date(a.release_date).getTime()
          case "desc_time":
            return new Date(a.release_date).getTime() - new Date(b.release_date).getTime()
        }
      });
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


import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import App from './App.vue'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Home from "@/views/Home";
import About from "@/views/About";
import axios from "axios";
import "./utils";
import Utils from "@/utils";

Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

Vue.config.productionTip = false

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/about',
      name: 'about',
      component: About
    }
  ]
})

const store = new Vuex.Store({
  state: {
    episodes: [],
    limit: 9,
    error: undefined,
    data: undefined,

    totalMembers: 0,
    totalCountries: 0,
    totalPlatforms: 0,
    totalAnimes: 0,
    totalEpisodes: 0,
    totalDuration: 0
  },
  mutations: {
    SET_LIMIT(state, payload) {
      state.limit = payload
    },

    SET_ARRAY_EPISODES(state, payload) {
      state.episodes = payload
    },

    SET_ERROR(state, payload) {
      state.error = payload
    },

    SET_DATA(state, payload) {
      state.data = payload
    },

    SET_MEMBERS(state, payload) {
      state.totalMembers = payload
    },

    SET_COUNTRIES(state, payload) {
      state.totalCountries = payload
    },

    SET_PLATFORMS(state, payload) {
      state.totalPlatforms = payload
    },

    SET_ANIMES(state, payload) {
      state.totalAnimes = payload
    },

    SET_EPISODES(state, payload) {
      state.totalEpisodes = payload
    },

    SET_TOTAL_DURATION(state, payload) {
      state.totalDuration = payload
    },
  },
  getters: {
    isEmpty: state => {
      return state.episodes === undefined || state.episodes.length === 0
    },
  },
  actions: {
    getEpisodes({commit}, limit = 9) {
      commit('SET_LIMIT', limit)

      axios.get(Utils.getLocalFile("php/lastest_episodes.php?country=France&limit=" + limit))
          .then(response => {
            commit('SET_ARRAY_EPISODES', response.data.episodes)

            axios.get(Utils.getLocalFile("php/data.php?country=France"))
                .then(response => {
                  commit('SET_DATA', response.data)

                  commit('SET_MEMBERS', response.data.total_members)
                  commit('SET_COUNTRIES', response.data.total_countries)
                  commit('SET_PLATFORMS', response.data.total_platforms)
                  commit('SET_ANIMES', response.data.total_animes)
                  commit('SET_EPISODES', response.data.total_episodes)
                  commit('SET_TOTAL_DURATION', response.data.total_duration)
                })
                .catch(error => {
                  commit('SET_ERROR', error)
                })
          })
          .catch(error => {
            commit('SET_ERROR', error)
          })
    }
  }
})

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')

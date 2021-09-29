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
    isLoaded: false,
    error: undefined
  },
  mutations: {
    SET_LOAD(state, payload) {
      state.isLoaded = payload
    },

    SET_LIMIT(state, payload) {
      state.limit = payload
    },

    SET_EPISODES(state, payload) {
      state.episodes = payload
    },

    SET_ERROR(state, payload) {
      state.error = payload
    },
  },
  actions: {
    getEpisodes({commit}, limit = 9) {
      commit('SET_LOAD', true)
      commit('SET_LIMIT', limit)

      console.log('Fetch...')
      console.log(limit)

      axios.get("http://localhost/example.php?limit=" + limit)
          .then(response => {
            commit('SET_EPISODES', response.data)
            commit('SET_LOAD', false)
          })
          .catch(error => {
            commit('SET_ERROR', error)
            commit('SET_LOAD', false)
          })
    }
  }
})

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')

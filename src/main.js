import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import App from './App.vue'
import VueSession from 'vue-session'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import Home from "@/views/Home";
import axios from "axios";
import "./utils";
import Utils from "@/utils";

Vue.use(VueSession, {
    persist: true
})

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
    ]
})

const store = new Vuex.Store({
    state: {
        limit: 9,
        error: null,
        fullData: null,
    },
    mutations: {
        SET_LIMIT(state, payload) {
            state.limit = payload
        },

        SET_ERROR(state, payload) {
            state.error = payload
        },

        SET_DATA(state, payload) {
            state.fullData = payload
        },
    },
    getters: {
        isEmpty: state => {
            return Utils.getInData(state.fullData, "latest_episodes", []).length === 0
        },

        getFavicon() {
            return Utils.getFile("images/favicon.jpg")
        },

        getJaisImage() {
            return Utils.getFile("images/jais.jpg")
        },
    },
    actions: {
        async getData({commit}, limit = 9) {
            commit('SET_LIMIT', limit)

            await axios.get(Utils.getLocalFile("php/data.php?country=France&limit=" + limit))
                .then(response => {
                    commit('SET_DATA', response.data)
                })
                .catch(error => {
                    commit('SET_ERROR', error)
                })
        },
    }
})

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#app')

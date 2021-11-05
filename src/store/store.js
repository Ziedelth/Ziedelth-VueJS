import Vuex from "vuex";
import Utils from "@/utils";
import axios from "axios";
import Vue from "vue";

Vue.use(Vuex)

export default new Vuex.Store({
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
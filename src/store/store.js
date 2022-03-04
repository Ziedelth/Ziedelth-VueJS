import Vuex from "vuex";
import Vue from "vue";
import Utils from "@/utils";

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        countries: [],
        currentCountry: null,

        token: null,
        user: null,
    },
    mutations: {
        SET_COUNTRIES(state, payload) {
            state.countries = payload
        },
        SET_CURRENT_COUNTRY(state, payload) {
            state.currentCountry = payload
        },

        SET_TOKEN(state, payload) {
            state.token = payload
        },

        SET_USER(state, payload) {
            state.user = payload
        }
    },
    actions: {
        setCountries({commit}, countries) {
            commit('SET_COUNTRIES', countries)
        },
        setCurrentCountry({commit}, currentCountry) {
            commit('SET_CURRENT_COUNTRY', currentCountry)
        },

        setToken({commit}, token) {
            commit('SET_TOKEN', token)
        },
        setUser({commit}, user) {
            commit('SET_USER', user)
        }
    },
    getters: {
        isLogin(state) {
            return Utils.isNotNull(state.user)
        }
    }
})
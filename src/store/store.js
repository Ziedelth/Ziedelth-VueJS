import Vuex from "vuex";
import Vue from "vue";
import Utils from "@/utils";

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        token: null,
        user: null,
    },
    mutations: {
        SET_TOKEN(state, payload) {
            state.token = payload
        },

        SET_USER(state, payload) {
            state.user = payload
        }
    },
    actions: {
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
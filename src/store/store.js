import Vuex from "vuex";
import Vue from "vue";
import Utils from "@/utils";

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        user: null,
    },
    mutations: {
        SET_USER(state, payload) {
            state.user = payload
        }
    },
    actions: {
        setUser({ commit }, user) {
            commit('SET_USER', user)
        }
    },
    getters: {
        isLogin(state) {
            return Utils.isNotNull(state.user)
        }
    }
})
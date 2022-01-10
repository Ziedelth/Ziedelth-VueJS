import Vuex from "vuex";
import Vue from "vue";

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
    getters: {
        isConnected: state => {
            return state.user != null;
        },
    },
    actions: {
        setUser(context, user) {
            context.commit('SET_USER', user)
        }
    }
})
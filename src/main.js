import Vue from 'vue'
import Vuex from 'vuex'
import App from './App.vue'
import VueSession from 'vue-session'
import store from "@/store/store"
import router from "@/router/router"

import 'bootstrap/dist/css/bootstrap.min.css'
import 'flag-icons/css/flag-icons.min.css'

Vue.use(VueSession, {
    persist: true
})

Vue.use(Vuex)

Vue.config.productionTip = false

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#app')

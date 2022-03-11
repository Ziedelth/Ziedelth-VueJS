import Vue from 'vue'
import Vuex from 'vuex'
import App from './App.vue'
import VueSession from 'vue-session'
import store from "@/store/store"
import router from "@/router/router"
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

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

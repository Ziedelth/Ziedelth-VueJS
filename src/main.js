import Vue from 'vue'
import Vuex from 'vuex'
import App from './App.vue'
import VueSession from 'vue-session'
import store from "@/store/store"
import router from "@/router/router"
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import LazyLoadDirective from "@/directives/LazyLoadDirective";

import 'bootstrap-vue/dist/bootstrap-vue.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(Vuex)

Vue.use(VueSession, {
    persist: true
})

Vue.config.productionTip = false

Vue.directive("lazyload", LazyLoadDirective);

new Vue({
    router,
    store,
    render: h => h(App),
}).$mount('#app')

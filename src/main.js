import Vue from 'vue'
import App from './App.vue'
import router from "@/router/router"
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

import 'bootstrap-vue/dist/bootstrap-vue.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

Vue.config.productionTip = false

new Vue({
    router,
    render: h => h(App),
}).$mount('#app')

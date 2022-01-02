import Vue from 'vue'
import App from './App.vue'
import VueSession from 'vue-session'
import router from "@/router/router"
import 'bootstrap/dist/css/bootstrap.min.css'

Vue.use(VueSession, {
    persist: true
})

Vue.config.productionTip = false

new Vue({
    router,
    render: h => h(App),
}).$mount('#app')

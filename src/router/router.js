import VueRouter from "vue-router";
import Vue from "vue";

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            component: () => import("@/views/Home")
        },
        {
            path: '/:catchAll(.*)',
            component: () => import("@/views/NotFound")
        }
    ]
})
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
            path: '/animes',
            component: () => import("@/views/Animes")
        },
        {
            path: '/cgu',
            component: () => import("@/views/CGU")
        },
        {
            path: '/:catchAll(.*)',
            component: () => import("@/views/NotFound")
        },
    ]
})
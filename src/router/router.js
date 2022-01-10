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
            path: '/login',
            component: () => import("@/views/Login")
        },
        {
            path: '/profile/:pseudo',
            component: () => import("@/views/Profile")
        },
        {
            path: '/privacy',
            component: () => import("@/views/CGU")
        },
        {
            path: '/:catchAll(.*)',
            component: () => import("@/views/NotFound")
        },
    ]
})
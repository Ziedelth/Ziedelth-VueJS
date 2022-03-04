import VueRouter from "vue-router";
import Vue from "vue";

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            component: () => import("@/views/HomeView")
        },
        {
            path: '/animes',
            component: () => import("@/views/AnimesView")
        },
        {
            path: '/anime/:id',
            component: () => import("@/views/AnimeView")
        },
        {
            path: '/login',
            component: () => import("@/views/users/LoginView")
        },
        {
            path: '/register',
            component: () => import("@/views/users/RegisterView")
        },
        {
            path: '/action/:hash',
            component: () => import("@/views/ActionView")
        },
        {
            path: '/password_reset',
            component: () => import("@/views/users/PasswordResetView")
        },
        {
            path: '/member/:pseudo',
            component: () => import("@/views/users/MemberView")
        },
        {
            path: '/settings',
            component: () => import("@/views/users/SettingsView")
        },
        {
            path: '/privacy',
            component: () => import("@/views/CGUView")
        },
        {
            path: '/:catchAll(.*)',
            component: () => import("@/views/NotFoundView")
        },
    ],
    scrollBehavior() {
        return {x: 0, y: 0};
    }
})
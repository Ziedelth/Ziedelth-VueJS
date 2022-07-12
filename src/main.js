import {createApp} from 'vue'
import {createRouter, createWebHistory} from 'vue-router'
import App from '@/App'

import HomeView from '@/views/HomeView'
import AnimesView from '@/views/AnimesView'
import AnimeView from '@/views/AnimeView'
import CGUView from '@/views/CGUView'
import NotFoundView from '@/views/NotFoundView'

import '@/assets/bootstrap.min.css'

const app = createApp(App)
app.use(createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: HomeView
        },
        {
            path: '/animes',
            component: AnimesView
        },
        {
            path: '/anime/:id',
            component: AnimeView
        },
        {
            path: '/privacy',
            component: CGUView
        },
        {
            path: '/:catchAll(.*)',
            component: NotFoundView
        },
    ],
}))
app.mount('#app')

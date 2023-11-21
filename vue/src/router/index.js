import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'
import HomeView from '../views/HomeView.vue'
import Transactions from "../components/transactions/Transactions.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/about',
            name: 'about',
            component: () => import('../views/AboutView.vue')
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../components/auth/Login.vue')
        },
        {
          path: "/transactions",
          name: "transactions",
          component: Transactions,
        },
    ]
})

router.beforeEach(async (to, from) => {
    const authStore = useAuthStore()
    const token = sessionStorage.getItem('token')

    if (token && !authStore.isAuthenticated) {
        try {
            axios.defaults.headers.common.Authorization = 'Bearer ' + token
            await authStore.loadUser()
        } catch (error) {
            sessionStorage.removeItem('token')
            return { name: 'login' }
        }
    }

    if (!authStore.isAuthenticated && to.name !== 'login') {
        return { name: 'login' }
    }

    if (authStore.isAuthenticated && to.name === 'login') {
        return { name: 'home' }
    }
})

export default router

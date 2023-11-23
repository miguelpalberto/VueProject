import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'
import HomeView from '../views/HomeView.vue'
import AboutView from '../views/AboutView.vue'
import Login from '../components/auth/Login.vue'
import Register from '../components/auth/Register.vue'
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
            component: AboutView
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/register',
            name: 'register',
            component: Register
        },
        {
          path: "/transactions",
          name: "transactions",
          component: Transactions,
        },
    ]
})

const publicRouteNames = ['login', 'register', '/']

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

    if (!authStore.isAuthenticated && !publicRouteNames.includes(to.name)) {
        return { name: 'login' }
    }

    if (authStore.isAuthenticated && to.name === 'login') {
        return { name: 'home' }
    }
})

export default router

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'
import HomeView from '../views/HomeView.vue'
import Dashboard from '../views/Dashboard.vue'
import Login from '../components/auth/Login.vue'
import Register from '../components/auth/Register.vue'
import Transactions from '../components/transactions/Transactions.vue'
import Transaction from '../components/transactions/Transaction.vue'
import Categories from '../components/categories/Categories.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: Dashboard
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
            path: '/transactions',
            name: 'transactions',
            component: Transactions
        },
        {
            path: '/transactions/create',
            name: 'createtransaction',
            component: Transaction
        },
        {
            path: '/vcards/:vcard/transactions/create',
            name: 'createvCardtransaction',
            component: Transaction,
            props: (route) => ({ vcard: route.params.vcard }),
        },
        {
            path: "/transactions/:id/edit",
            name: "editTransaction",
            component: Transaction,
            props: (route) => ({ id: parseInt(route.params.id) }),
        },
        {
            path: '/categories',
            name: 'categories',
            component: Categories
        },
    ]
})

const publicRouteNames = ['login', 'register', 'home']

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
        return { name: 'home' }
    }

    if (authStore.isAuthenticated && publicRouteNames.includes(to.name)) {
        return { name: 'dashboard' }
    }
})

export default router

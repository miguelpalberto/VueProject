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
import ChangePassword from '../components/auth/ChangePassword.vue'
import ChangeConfirmationCode from '../components/auth/ChangeConfirmationCode.vue'
import Profile from '../components/users/Profile.vue'
import Admins from '../components/admins/Admins.vue'

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
            path: '/profile',
            name: 'profile',
            component: Profile
        },
        {
            path: '/change-password',
            name: 'changePassword',
            component: ChangePassword
        },
        {
            path: '/change-confirmation-code',
            name: 'changeConfirmationCode',
            component: ChangeConfirmationCode
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
            props: (route) => ({ vcard: route.params.vcard })
        },
        {
            path: '/transactions/:id/edit',
            name: 'editTransaction',
            component: Transaction,
            props: (route) => ({ id: parseInt(route.params.id) })
        },
        {
            path: '/categories',
            name: 'categories',
            component: Categories
        },
        { path: '/admins', name: 'admins', component: Admins },
        {
            path: '/admins/:id',
            name: 'User',
            component: Admins,
            //props: true,
            // Replaced with the following line to ensure that id is a number
            props: (route) => ({ id: parseInt(route.params.id) })
        }
    ]
})

const publicRouteNames = ['login', 'register', 'home']
const vcardOnlyRouteNames = ['changeConfirmationCode', 'transactions', 'createtransaction']
const adminOnlyRouteNames = ['createvCardtransaction']

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    const token = sessionStorage.getItem('token')

    if (token && !authStore.isAuthenticated) {
        try {
            axios.defaults.headers.common.Authorization = 'Bearer ' + token
            await authStore.loadUser()
        } catch (error) {
            sessionStorage.removeItem('token')
            return next({ name: 'login' })
        }
    }

    if (!authStore.isAuthenticated && !publicRouteNames.includes(to.name)) {
        return next({ name: 'home' })
    }

    if (authStore.isAuthenticated && publicRouteNames.includes(to.name)) {
        return next({ name: 'dashboard' })
    }

    //vcard only routes
    if (authStore.isAuthenticated && authStore.isAdmin && vcardOnlyRouteNames.includes(to.name)) {
        return next({ name: 'dashboard' })
    }

    //admin only routes
    if (authStore.isAuthenticated && !authStore.isAdmin && adminOnlyRouteNames.includes(to.name)) {
        return next({ name: 'dashboard' })
    }

    next()
})

export default router

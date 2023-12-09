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
import ChangePassword from '../components/users/ChangePassword.vue'
import ChangeConfirmationCode from '../components/users/ChangeConfirmationCode.vue'
import Profile from '../components/users/Profile.vue'
import DismissVCard from '../components/users/DismissVCard.vue'
import Admins from '../components/admins/Admins.vue'
import Category from '../components/categories/Category.vue'
import VCards from '../components/vcards/VCards.vue'
import Easteregg from '../components/auth/Easteregg.vue'


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
            path: '/easteregg',
            name: 'easteregg',
            component: Easteregg
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
            name: 'createTransaction',
            component: Transaction
        },

        {
            path: '/vcards/:vcard/transactions/create',
            name: 'createvCardTransaction',
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
            path: '/admins',
            name: 'admins',
            component: Admins
        },
        { 
            path: '/dismissVcard',
            name: 'dismissVcard',
            component: DismissVCard
        },
        {
            path: '/categories',
            name: 'categories',
            component: Categories
        },
        {
            path: '/categories/create',
            name: 'createcategory',
            component: Category
        },
        {
            path: '/categories/:id',//path: '/categories/:id/edit',
            name: 'editCategory',
            component: Category,
            props: (route) => ({ id: parseInt(route.params.id) })
        },
        {
            path: '/vcards',
            name: 'vcards',
            component: VCards
        }
    ]
})

const publicRouteNames = ['login', 'register', 'home', 'easteregg']
const vcardOnlyRouteNames = ['changeConfirmationCode', 'transactions', 'createTransaction', 'categories', 'editTransaction', 'dismissVcard']
const adminOnlyRouteNames = ['createvCardTransaction', 'admins', 'vcards']

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

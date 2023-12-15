//import './assets/main.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import "vue-toastification/dist/index.css";
import './assets/main.css'
import { vue3Debounce } from 'vue-debounce'
import "bootstrap"
import { Bootstrap5Pagination } from 'laravel-vue-pagination';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { io } from 'socket.io-client'

import App from './App.vue'
import router from './router'
import Toast from "vue-toastification";

import FieldErrorMessage from './components/global/FieldErrorMessage.vue'
import ConfirmationDialog from './components/global/ConfirmationDialog.vue'

const app = createApp(App)

const apiDomain = import.meta.env.VITE_API_DOMAIN
const externalApiDomain = import.meta.env.VITE_EXTERNAL_API_DOMAIN
const wsConnection = import.meta.env.VITE_WS_CONNECTION

axios.defaults.baseURL = apiDomain + '/api'
axios.defaults.headers.common['Content-type'] = 'application/json'

const axiosExternal = axios.create({
    baseURL: externalApiDomain + '/api',
    headers: {
        'Content-type': 'application/json'
    }
})

app.use(createPinia())
app.use(router)

app.use(Toast, {
    position: "top-center",
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: true,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false,
});


app.provide('serverUrl',`${apiDomain}`)
app.provide('apiUrl',`${apiDomain}/api`)
app.provide('axiosExternal', axiosExternal)
app.provide('socket',io(wsConnection))

app.component('FieldErrorMessage', FieldErrorMessage)
app.component('ConfirmationDialog', ConfirmationDialog)
app.component('Bootstrap5Pagination', Bootstrap5Pagination)

app.directive('debounce', vue3Debounce({lock: true}))

app.mount('#app')

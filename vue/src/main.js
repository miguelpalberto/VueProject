//import './assets/main.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import "vue-toastification/dist/index.css";
import "bootstrap"

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { io } from 'socket.io-client'

import App from './App.vue'
import router from './router'
import Toast from "vue-toastification";
import { setMask } from 'simple-mask-money'

const app = createApp(App)

const apiDomain = import.meta.env.VITE_API_DOMAIN
const externalApiDomain = import.meta.env.VITE_EXTERNAL_API_DOMAIN
const wsConnection = import.meta.env.VITE_WS_CONNECTION

axios.defaults.baseURL = apiDomain + '/api'
axios.defaults.headers.common['Content-type'] = 'application/json'

const axiousExternal = axios.create({
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
app.provide('axiosExternal', axiousExternal)
//app.provive('socket',io(wsConnection))

app.mount('#app')

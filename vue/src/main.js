//import './assets/main.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import "bootstrap"


import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { io } from 'socket.io-client'

import App from './App.vue'
import router from './router'

const app = createApp(App)

const apiDomain = import.meta.env.VITE_API_DOMAIN
const wsConnection = import.meta.env.VITE_WS_CONNECTION

axios.defaults.baseURL = apiDomain + '/api'
axios.defaults.headers.common['Content-type'] = 'application/json'

app.use(createPinia())
app.use(router)

app.provide('serverUrl',`${apiDomain}/api`)
//app.provive('socket',io(wsConnection))


// Default Axios configuration

app.mount('#app')



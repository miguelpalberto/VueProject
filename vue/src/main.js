import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

const app = createApp(App)

const apiDomain = import.meta.env.VITE_API_DOMAIN//tutorial
const wsConnection = import.meta.env.VITE_WS_CONNECTION//tutorial

app.use(createPinia())
app.use(router)

app.provide('serverUrl',`${apiDomain}/api`)//tutorial
app.provive('socket',io(wsConnection))//tutorial

app.mount('#app')



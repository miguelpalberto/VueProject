<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";


const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const credentials = ref({
  username: '',
  password: ''
})


const isLoading = ref(false)

const login = async () => {
  isLoading.value = true

  try {
    const response = await axios.post('/auth/login', credentials.value)
    axios.defaults.headers.common.Authorization = `Bearer ${response.data.access_token}`
    sessionStorage.setItem('token', response.data.access_token)
    await authStore.loadUser()
    router.push({ name: 'home' })
    toast.success('Login successful')
  }
  catch (error) {
    delete axios.defaults.headers.common.Authorization
    authStore.clearUser()
    credentials.value.password = ''
    toast.error('User credentials are invalid')
  }
  finally {
    isLoading.value = false
  }
}

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="login">
    <h3 class="mt-5 mb-3">Login</h3>
    <hr>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputUsername" class="form-label">Username</label>
        <input type="text" class="form-control" id="inputUsername" required v-model="credentials.username">
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label for="inputPassword" class="form-label">Password</label>
        <input type="password" class="form-control" id="inputPassword" required v-model="credentials.password">
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="login" :disabled="isLoading">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
        <span role="login">Login</span>
      </button>

    </div>
  </form>
</template>


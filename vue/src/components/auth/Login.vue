<script setup>
import { ref } from 'vue'
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
    console.log(error)
    if (error.response.data?.error){
      toast.error('User credentials are invalid')
    }
    else{
      toast.error('Something went wrong. Please try again')
    }
    delete axios.defaults.headers.common.Authorization
    authStore.clearUser()
    credentials.value.password = ''
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
    <div class="d-flex justify-content-center">
      <button type="submit" class="btn btn-primary px-5" @click="login" :disabled="isLoading">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
        <span role="login">Sign In</span>
      </button>
    </div>
    <div class="d-flex flex-row justify-content-center align-items-center">
      <b style="margin-top:3px">Don't have an account?</b>
      <router-link class="btn btn-link" to="/register">Sign up now!</router-link>
    </div>
  </form>
</template>


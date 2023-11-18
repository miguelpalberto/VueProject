<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";


const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const isLoading = ref(false)
const errors = ref({})
const credentials = ref({
  phone_number: '',
  password: '',
  confirmation_code: '',
  name: '',
  email: '',
})

const confirmPassword = ref('')
const confirmConfirmationCode = ref('')
const confirmPasswordIsValid = computed(() => {
  return credentials.value.password === confirmPassword.value
})
const confirmConfirmationCodeIsValid = computed(() => {
  return credentials.value.confirmation_code === confirmConfirmationCode.value
})

watch(confirmPasswordIsValid, (isValid) => {
  if (isValid) {
    delete errors.value.confirmPassword
  }
  else {
    errors.value.confirmPassword = ['Passwords do not match']
  }
})

watch(confirmConfirmationCodeIsValid, (isValid) => {
  if (isValid) {
    delete errors.value.confirmConfirmationCode
  }
  else {
    errors.value.confirmConfirmationCode = ['Confirmation codes do not match']
  }
})

const register = async () => {
  if (!confirmPasswordIsValid.value || !confirmConfirmationCodeIsValid.value) {
    return
  }

  isLoading.value = true
  errors.value = {}

  try {
    const response = await axios.post('/vcards', credentials.value)
    axios.defaults.headers.common.Authorization = `Bearer ${response.data.access_token}`
    sessionStorage.setItem('token', response.data.access_token)
    await authStore.loadUser()
    router.push({ name: 'home' })
    toast.success('Registration successful')
  }
  catch (error) {
    if (error.response.status === 422) {
      console.log('422 hit')
      errors.value = error.response.data.errors
    }
    else {
      toast.error('An error occurred while registering')
    }
  }
  finally {
    isLoading.value = false
  }
}

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="register">
    <h3 class="mt-5 mb-3">Register</h3>
    <hr>
    <div class="mb-1">
      <label for="inputUsername" class="form-label">Phone Number</label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.phone_number }" id="inputPhone" :disabled="isLoading" required v-model="credentials.phone_number">
      <div class="invalid-feedback" v-if="errors && errors.phone_number">
        {{ errors.phone_number[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputName" class="form-label">Name</label>
      <input type="text" :class="{ 'is-invalid': errors && errors.name }" class="form-control" id="inputName" :disabled="isLoading"  required
        v-model="credentials.name">
      <div class="invalid-feedback" v-if="errors && errors.name">
        {{ errors.name[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputEmail" class="form-label">Email</label>
      <input type="text" :class="{ 'is-invalid': errors && errors.email }" class="form-control" id="inputEmail" :disabled="isLoading"  required
        v-model="credentials.email">
      <div class="invalid-feedback" v-if="errors && errors.email">
        {{ errors.email[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputConfirmationCode" class="form-label">Confirmation Code</label>
      <input type="password" class="form-control"
        :class="{ 'is-invalid': errors && errors.confirmation_code || !confirmConfirmationCodeIsValid }"
        id="inputConfirmationCode" :disabled="isLoading" required v-model="credentials.confirmation_code">
      <div class="invalid-feedback" v-if="errors && errors.confirmation_code">
        {{ errors.confirmation_code[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputConfirmConfirmationCode" class="form-label">Confirm Confirmation Code</label>
      <input type="password" :class="{ 'is-invalid': !confirmConfirmationCodeIsValid }" class="form-control"
        id="inputConfirmConfirmationCode" :disabled="isLoading" required v-model="confirmConfirmationCode">
      <div class="invalid-feedback" v-if="errors && errors.confirmConfirmationCode">
        {{ errors.confirmConfirmationCode[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputPassword" class="form-label">Password</label>
      <input type="password" class="form-control"
        :class="{ 'is-invalid': errors && errors.password || !confirmPasswordIsValid }" id="inputPassword" :disabled="isLoading" required
        v-model="credentials.password">
      <div class="invalid-feedback" v-if="errors && errors.password">
        {{ errors.password[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
      <input type="password" :class="{ 'is-invalid': !confirmPasswordIsValid }" class="form-control"
        id="inputConfirmPassword" :disabled="isLoading" required v-model="confirmPassword">
      <div class="invalid-feedback" v-if="errors && errors.confirmPassword">
        {{ errors.confirmPassword[0] }}
      </div>
    </div>
    <div class="mb-2 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="register" :disabled="isLoading">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
        <span role="login">Register</span>
      </button>
    </div>
  </form>
</template>



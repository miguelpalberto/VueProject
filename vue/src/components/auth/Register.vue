<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import AvatarPreviewer from '../images/AvatarPreviewer.vue';
import avatarNoneUrl from "@/assets/avatar-none.png";

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
  photo_file: null
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

const onInputFileChanged = (file) => {
  credentials.value.photo_file = file
}

const register = async () => {
  if (!confirmPasswordIsValid.value || !confirmConfirmationCodeIsValid.value) {
    return
  }

  isLoading.value = true
  errors.value = {}

  try {
    let formData = new FormData()
    formData.append('phone_number', credentials.value.phone_number)
    formData.append('password', credentials.value.password)
    formData.append('confirmation_code', credentials.value.confirmation_code)
    formData.append('name', credentials.value.name)
    formData.append('email', credentials.value.email)
    formData.append('photo_file', credentials.value.photo_file)
    const response = await axios.post('/vcards', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    axios.defaults.headers.common.Authorization = `Bearer ${response.data.access_token}`
    sessionStorage.setItem('token', response.data.access_token)
    await authStore.loadUser()
    toast.success('Registration successful')
    router.push({ name: 'home' })
  }
  catch (error) {
    if (error.response.status === 422) {
      errors.value = error.response.data.errors
    }
    else {
      toast.error('An error occurred while registering')
    }
  }
  finally {
    axios.defaults.headers.common.ContentType = 'application/json'
    isLoading.value = false
  }
}

</script>

<template>
  <form class="row g-2 needs-validation" novalidate @submit.prevent="register">
    <h3 class="mt-5 mb-3">Register</h3>
    <hr>
    <div class="mb-1">
      <AvatarPreviewer @input-file-changed="onInputFileChanged" :is-parent-loading="isLoading" :imgUrl="avatarNoneUrl" :alt="Avatar" :allowUpload="true"
        :allowDelete="false" />
      <div class="invalid-feedback" v-if="errors && errors.photo_file">
        {{ errors.photo_file[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputPhone" class="form-label">Phone Number<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.phone_number }" id="inputPhone"
        :disabled="isLoading" required v-model="credentials.phone_number">
      <div class="invalid-feedback" v-if="errors && errors.phone_number">
        {{ errors.phone_number[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputName" class="form-label">Name<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="text" :class="{ 'is-invalid': errors && errors.name }" class="form-control" id="inputName"
        :disabled="isLoading" required v-model="credentials.name">
      <div class="invalid-feedback" v-if="errors && errors.name">
        {{ errors.name[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputEmail" class="form-label">Email<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="text" :class="{ 'is-invalid': errors && errors.email }" class="form-control" id="inputEmail"
        :disabled="isLoading" required v-model="credentials.email">
      <div class="invalid-feedback" v-if="errors && errors.email">
        {{ errors.email[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputConfirmationCode" class="form-label">Confirmation Code<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" class="form-control"
        :class="{ 'is-invalid': errors && errors.confirmation_code || !confirmConfirmationCodeIsValid }"
        id="inputConfirmationCode" :disabled="isLoading" required v-model="credentials.confirmation_code">
      <div class="invalid-feedback" v-if="errors && errors.confirmation_code">
        {{ errors.confirmation_code[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputConfirmConfirmationCode" class="form-label">Confirm Confirmation Code<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" :class="{ 'is-invalid': !confirmConfirmationCodeIsValid }" class="form-control"
        id="inputConfirmConfirmationCode" :disabled="isLoading" required v-model="confirmConfirmationCode">
      <div class="invalid-feedback" v-if="errors && errors.confirmConfirmationCode">
        {{ errors.confirmConfirmationCode[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputPassword" class="form-label">Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" class="form-control"
        :class="{ 'is-invalid': errors && errors.password || !confirmPasswordIsValid }" id="inputPassword"
        :disabled="isLoading" required v-model="credentials.password">
      <div class="invalid-feedback" v-if="errors && errors.password">
        {{ errors.password[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputConfirmPassword" class="form-label">Confirm Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" :class="{ 'is-invalid': !confirmPasswordIsValid }" class="form-control"
        id="inputConfirmPassword" :disabled="isLoading" required v-model="confirmPassword">
      <div class="invalid-feedback" v-if="errors && errors.confirmPassword">
        {{ errors.confirmPassword[0] }}
      </div>
    </div>
    <div class="mt-2 d-flex justify-content-center">
      <button type="submit" class="btn btn-primary px-5" @click="register" :disabled="isLoading">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
        <span role="login">Register</span>
      </button>
    </div>
  </form>
</template>



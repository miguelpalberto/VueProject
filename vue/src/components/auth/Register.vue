<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import AvatarPreviewer from '../users/AvatarPreviewer.vue';
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
  photo_file: null,

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
    if (credentials.value.photo_file) {
      formData.append('photo_file', credentials.value.photo_file)
    }

    await authStore.register(formData)
      .then(() => {
        toast.success('Registration successful')
        router.push({ name: 'home' })
      })
  }
  catch (error) {
    console.log(error)
    if (error.response?.status === 422) {
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
  <div class="container-sm mb-5" style="max-width: 450px;">
    <form class="row g-2 needs-validation" novalidate @submit.prevent="register">
      <h3 class="mt-5 mb-3">Register</h3>
      <hr>
      <div class="mb-1">
        <AvatarPreviewer @input-file-changed="onInputFileChanged" :is-parent-loading="isLoading"
          :imgUrl="avatarNoneUrl" />
        <field-error-message :errors="errors" fieldName="photo_file"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputPhone" class="form-label">Phone Number<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span>
        </label>
        <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.phone_number }" id="inputPhone"
          :disabled="isLoading" required v-model="credentials.phone_number">
        <field-error-message :errors="errors" fieldName="phone_number"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputName" class="form-label">Name<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span></label>
        <input type="text" :class="{ 'is-invalid': errors && errors.name }" class="form-control" id="inputName"
          :disabled="isLoading" required v-model="credentials.name">
        <field-error-message :errors="errors" fieldName="name"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputEmail" class="form-label">Email<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span></label>
        <input type="text" :class="{ 'is-invalid': errors && errors.email }" class="form-control" id="inputEmail"
          :disabled="isLoading" required v-model="credentials.email">
        <field-error-message :errors="errors" fieldName="email"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputConfirmationCode" class="form-label">Confirmation Code<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span></label>
        <input type="password" class="form-control"
          :class="{ 'is-invalid': errors && errors.confirmation_code || !confirmConfirmationCodeIsValid }"
          id="inputConfirmationCode" :disabled="isLoading" required v-model="credentials.confirmation_code">
        <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputConfirmConfirmationCode" class="form-label">Confirm Confirmation Code<span
            class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span></label>
        <input type="password" :class="{ 'is-invalid': !confirmConfirmationCodeIsValid }" class="form-control"
          id="inputConfirmConfirmationCode" :disabled="isLoading" required v-model="confirmConfirmationCode">
        <field-error-message :errors="errors" fieldName="confirmConfirmationCode"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputPassword" class="form-label">Password<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span></label>
        <input type="password" class="form-control"
          :class="{ 'is-invalid': errors && errors.password || !confirmPasswordIsValid }" id="inputPassword"
          :disabled="isLoading" required v-model="credentials.password">
        <field-error-message :errors="errors" fieldName="password"></field-error-message>
      </div>
      <div class="mb-1">
        <label for="inputConfirmPassword" class="form-label">Confirm Password<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span></label>
        <input type="password" :class="{ 'is-invalid': !confirmPasswordIsValid }" class="form-control"
          id="inputConfirmPassword" :disabled="isLoading" required v-model="confirmPassword">
        <field-error-message :errors="errors" fieldName="confirmPassword"></field-error-message>
      </div>
      <div class="mt-4 d-flex justify-content-center">
        <button type="submit" class="btn btn-primary px-5" @click="register" :disabled="isLoading || !confirmPasswordIsValid || !confirmConfirmationCodeIsValid">
          <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
          <span role="register">Register</span>
        </button>
      </div>
    </form>
  </div>
</template>



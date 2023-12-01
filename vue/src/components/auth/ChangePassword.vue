<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification";

const router = useRouter()
const toast = useToast()

const formData = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const errors = ref({})
const isLoading = ref(false)

const confirmPasswordIsValid = computed(() => {
  return formData.value.new_password === formData.value.new_password_confirmation
})

watch(confirmPasswordIsValid, (isValid) => {
  if (isValid) {
    delete errors.value.new_password_confirmation
  }
  else {
    errors.value.new_password_confirmation = ['Passwords do not match']
  }
})

const changePassword = async () => {
  isLoading.value = true

  try {
    await axios.patch(`/authUsers/changePassword`, formData.value)
    router.push({ name: 'home' })
    toast.success('Passoword changed successfully')
  }
  catch (error) {
    if (error.response.status === 422) {
      errors.value = error.response.data.errors
    }
    toast.error('Something went wrong please try again')
  }
  finally {
    isLoading.value = false
  }
}

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="changePassword">
    <h3 class="mt-5 mb-3">Change Password</h3>
    <hr>
    <div class="mb-1">
      <label for="inputCurrentPassword" class="form-label">Current Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" class="form-control"
        :class="{ 'is-invalid': errors && errors.current_password }" id="inputCurrentPassword"
        :disabled="isLoading" required v-model="formData.current_password">
      <div class="invalid-feedback" v-if="errors && errors.current_password">
        {{ errors.current_password[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputNewPassword" class="form-label">New Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" :class="{ 'is-invalid': !confirmPasswordIsValid || (errors && (errors.new_password || errors.new_password_confirmation)) }" class="form-control"
        id="inputNewPassword" :disabled="isLoading" required v-model="formData.new_password">
      <div class="invalid-feedback" v-if="errors && errors.new_password">
        {{ errors.new_password[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputNewPasswordConfirm" class="form-label">Confirm New Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" :class="{ 'is-invalid': !confirmPasswordIsValid || errors && errors.new_password_confirmation }" class="form-control"
        id="inputNewPasswordConfirm" :disabled="isLoading" required v-model="formData.new_password_confirmation">
      <div class="invalid-feedback" v-if="errors && errors.new_password_confirmation">
        {{ errors.new_password_confirmation[0] }}
      </div>
    </div>
    <div class="d-flex justify-content-center">
      <button type="submit" class="btn btn-primary px-5" @click="changePassword" :disabled="isLoading || !confirmPasswordIsValid">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
        <span role="login">Submit</span>
      </button>
    </div>
  </form>
</template>


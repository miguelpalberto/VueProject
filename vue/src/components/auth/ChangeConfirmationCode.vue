<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const formData = ref({
  password: '',
  confirmation_code: '',
  confirmation_code_confirmation: ''
})

const errors = ref({})
const isLoading = ref(false)

const confirmCodeIsValid = computed(() => {
  return formData.value.confirmation_code === formData.value.confirmation_code_confirmation
})

watch(confirmCodeIsValid, (isValid) => {
  if (isValid) {
    delete errors.value.confirmation_code_confirmation
  }
  else {
    errors.value.confirmation_code_confirmation = ['Confirmation Codes do not match']
  }
})

const changeConfirmationCode = async () => {
  isLoading.value = true

  try {
    await axios.patch(`/vcards/${authStore.user.username}/changeConfirmationCode`, formData.value)
    router.push({ name: 'home' })
    toast.success('Confirmation code changed successfully')
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
  <form class="row g-3 needs-validation" novalidate @submit.prevent="changeConfirmationCode">
    <h3 class="mt-5 mb-3">Change Confirmation Code</h3>
    <hr>
    <div class="mb-1">
      <label for="inputPassword" class="form-label">Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" class="form-control"
        :class="{ 'is-invalid': errors && errors.password }" id="inputPassword"
        :disabled="isLoading" required v-model="formData.password">
      <field-error-message :errors="errors" fieldName="password"></field-error-message>
    </div>
    <div class="mb-1">
      <label for="inputNewConfirmationCode" class="form-label">New Confirmation Code<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" :class="{ 'is-invalid': !confirmCodeIsValid || (errors && (errors.confirmation_code || errors.confirmation_code_confirmation)) }" class="form-control"
        id="inputNewConfirmationCode" :disabled="isLoading" required v-model="formData.confirmation_code">
      <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
    </div>
    <div class="mb-1">
      <label for="inputConfirmationCodeConfirm" class="form-label">Confirm New Confirmation Code<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span></label>
      <input type="password" :class="{ 'is-invalid': !confirmCodeIsValid || errors && errors.confirmation_code_confirmation }" class="form-control"
        id="inputConfirmationCodeConfirm" :disabled="isLoading" required v-model="formData.confirmation_code_confirmation">
        <field-error-message :errors="errors" fieldName="confirmation_code_confirmation"></field-error-message>
    </div>
    <div class="d-flex justify-content-center">
      <button type="submit" class="btn btn-primary px-5" @click="changeConfirmationCode" :disabled="isLoading || !confirmCodeIsValid">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
        <span role="login">Submit</span>
      </button>
    </div>
  </form>
</template>


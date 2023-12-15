<script setup>
import { ref, computed, inject } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useToast } from "vue-toastification";
import { useAuthStore } from '../../stores/auth';


const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()

const formData = ref({
    password: '',
    confirmation_code: ''
})
const dismissConfirmationDialog = ref(null)
const errors = ref({})
const isLoading = ref(false)
const socket = inject('socket')

const isAbleToDismiss = computed(() => {
    if (!authStore.user) {
        return false
    }
    return authStore.user.balance === '0.00'
})

const cardText = computed(() => {
    if (isAbleToDismiss.value) {
        return 'Warning: Dismissing your vCard is irreversible and will permanently delete all associated data. Please provide your current password and confirmation code to proceed.'
    }
    else {
        return 'You must have a balance of 0 to dismiss your vCard'
    }
})

const dismiss = () => {
    dismissConfirmationDialog.value.show()
}

const dismissConfirmation = async (isConfirmed) => {
    if (!isConfirmed) {
        toast.info('Dismissal cancelled')
    }
    else {
        isLoading.value = true
        try {
            await axios.delete(`/vcards/${authStore.user.username}`, { data: formData.value })
            socket.emit('userDeleted', authStore.user, false)
            authStore.logout()
            toast.success('vCard dismissed successfully')
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
}

const cancel = () => {
    router.push({ name: 'profile' })
}

</script>
<template>
    <div class="d-flex justify-content-center">
        <div class="card text-bg-warning mb-3" style="max-width: 30rem;">
            <div class="card-body">
                <h6 class="card-text">{{ cardText }}</h6>
            </div>
        </div>
    </div>

    <confirmation-dialog ref="dismissConfirmationDialog" confirmationBtn="Confirm Dimiss vCard"
        :msg="`Are you sure you want to dismiss your vCard?\nTHIS ACTION IS IRREVERSIBLE AND WILL PERMANENTLY DELETE ALL ASSOCIATED DATA`"
        @response="dismissConfirmation" />
    <form class="row g-3 needs-validation" novalidate @submit.prevent="changeConfirmationCode">
        <h3 class="mb-3">Dismiss vCard</h3>
        <hr>
        <div class="mb-1">
            <label for="inputPassword" class="form-label">Password<span class="text-danger">*</span>
                &nbsp;<span class="text-muted">(required)</span></label>
            <input type="password" class="form-control" :class="{ 'is-invalid': errors && errors.password }"
                id="inputPassword" :disabled="isLoading || !isAbleToDismiss" required v-model="formData.password">
            <field-error-message :errors="errors" fieldName="password"></field-error-message>
        </div>
        <div class="mb-1">
            <label for="inputConfirmationCode" class="form-label">Confirmation Code<span class="text-danger">*</span>
                &nbsp;<span class="text-muted">(required)</span></label>
            <input type="password" :class="{ 'is-invalid': errors && errors.confirmation_code }" class="form-control"
                id="inputConfirmationCode" :disabled="isLoading || !isAbleToDismiss" required
                v-model="formData.confirmation_code">
            <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
        </div>
        <div class="mb-1 d-flex justify-content-center gap-3">
            <button class="btn btn-dark" type="button" :disabled="isLoading" @click="cancel">
                Cancel
            </button>
            <button class="btn btn-danger" type="button" :disabled="isLoading || !isAbleToDismiss" @click="dismiss">
                <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
                <span role="Dismiss">Dismiss</span>
            </button>
        </div>
    </form>
</template>
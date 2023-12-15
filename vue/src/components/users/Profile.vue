<script setup>
import { ref, onMounted, inject } from 'vue'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import AvatarPreviewer from '../users/AvatarPreviewer.vue';

const authStore = useAuthStore();
const toast = useToast()
const socket = inject('socket')

const isLoading = ref(false)
const errors = ref(null)
const isEditing = ref(false)

const formData = ref({
    name: '',
    email: '',
})

const cancelEditing = () => {
    errors.value = null
    isEditing.value = false
    formData.value.name = authStore.user.name
    formData.value.email = authStore.user.email
}

const toggleEditing = () => {
    isEditing.value = !isEditing.value
}

const save = () => {
    errors.value = null
    updateConfirmationDialog.value.show()
}

const username = authStore.user.username

onMounted(() => {
    formData.value.name = authStore.user.name
    formData.value.email = authStore.user.email
})

const updateConfirmationDialog = ref(null)

const updateProfileConfirmed = async (isConfirmed) => {
    if (!isConfirmed) {
        cancelEditing()
    }
    else{
        isLoading.value = true
        try {
            await authStore.updateProfile(formData.value)
            cancelEditing()
            toast.success('Profile updated successfully')
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

</script>
<template>
    <confirmation-dialog ref="updateConfirmationDialog" confirmationBtn="Update Profile"
        :msg="`Are you sure you want to update your profile?`"
        @response="updateProfileConfirmed" />
    <AvatarPreviewer v-if="!authStore.isAdmin" :is-parent-loading="isLoading || isEditing" :imgUrl="authStore.userPhotoUrl"
        :allow-upload-in-component="true" :allowDelete="true" />
    <div class="mb-1" v-if="!authStore.isAdmin">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" :disabled="true" :value="username">
    </div>
    <div class="mb-1">
        <label for="username" class="form-label">Email</label>
        <input type="text" class="form-control" id="inputEmail" :disabled="!isEditing || isLoading" v-model="formData.email"
            autocomplete="email">
        <field-error-message :errors="errors" fieldName="email"></field-error-message>
    </div>
    <div class="mb-1">
        <label for="username" class="form-label">Name</label>
        <input type="text" class="form-control" id="inputName" :disabled="!isEditing || isLoading" v-model="formData.name"
            autocomplete="name">
        <field-error-message :errors="errors" fieldName="name"></field-error-message>
    </div>
    <div class="mt-4 d-flex justify-content-center gap-5">
        <button class="btn btn-dark" v-if="isEditing" :disabled="isLoading" @click="cancelEditing">Cancel</button>
        <button class="btn btn-primary" v-if="!isEditing" @click="toggleEditing">Edit</button>
        <button class="btn btn-success" v-if="isEditing" :disabled="isLoading" @click="save">
            <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
            <span role="Save">Save</span>
        </button>
    </div>
    <div class="mt-4 d-grid gap-3 justify-content-center">
        <div class="g-col-12">
            <router-link class="btn btn-dark w-100" :class="{ disabled: isEditing }" :disabled="isEditing" :to="{ name: 'changePassword' }">
                
                <i class="bi bi-key-fill"></i>
                Change Password
            </router-link>
        </div>
        <div class="g-col-12" v-if="!authStore.isAdmin">
            <router-link class="btn btn-dark w-100" :class="{ disabled: isEditing }" :to="{ name: 'changeConfirmationCode' }">
                <i class="bi bi-person-vcard"></i>
                Change Confirmation Code
            </router-link>
        </div>
        <div class="g-col-12" v-if="!authStore.isAdmin">
            <router-link class="btn btn-danger w-100" :class="{ disabled: isEditing }" :to="{ name: 'dismissVcard' }">
                Dismiss VCard
            </router-link>
        </div>
    </div>
</template>
<style scoped>
    .disabled {
        pointer-events: none;
        cursor: default;
        opacity: 0.5;
    }
</style>
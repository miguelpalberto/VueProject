<script setup>
import axios from 'axios'
import { ref, computed, watch } from 'vue'
import { useToast } from "vue-toastification";
import { useRouter } from 'vue-router'
import AdminDetail from "./AdminDetail.vue"

const router = useRouter()
const toast = useToast()
const newUser = () => {
    return {
        id: null,
        name: '',
        email: '',
        photo_url: null
    }
}
const isLoading = ref(false)
const admin = ref(newUser())
const errors = ref({})

const validateInsert = () => {
    let isValid = true
    if (!admin.value.name || admin.value.name.trim() === '') {
        errors.value.name = ['Name is required']
        isValid = false

    } else if (admin.value.name.length > 255) {
        errors.value.name = ['Name must not exceed 255 characters']
        isValid = false
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!admin.value.email || admin.value.email.trim() === '') {
        errors.value.email = ['Email is required']
        isValid = false
    } else if (!emailRegex.test(admin.value.email.trim())) {
        errors.value.email = ['Invalid email format']
        isValid = false

    } else if (admin.value.email.length > 255) {
        errors.value.email = ['Email must not exceed 255 characters']
        isValid = false
    }

    if (!admin.value.password || admin.value.password.length < 8) {
        errors.value.password = ['Password must have at least 8 characters'];
        isValid = false;
    } else if (admin.value.password.length > 255) {
        errors.value.password = ['Password must not exceed 255 characters']
        isValid = false
    }

    return isValid
}

const save = () => {
    isLoading.value = true

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    axios.post('users', admin.value)
        .then(() => {
            isLoading.value = false
            router.push({ name: 'admins' })
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }
            isLoading.value = false
            toast.error('Error creating administrator')
        })
        .finally(() => {
            isLoading.value = false
        })
}

const cancel = () => {
    router.push({ name: 'admins' })
}
</script>

<template>
    <admin-detail :admin="admin" :errors="errors" @save="save" @cancel="cancel" />
</template>
<script setup>
import axios from 'axios'
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useToast } from 'vue-toastification'
import CategoryDetail from './CategoryDetail.vue'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const newCategory = () => {
    return {
        id: null,
        vcard: authStore.user.username,
        type: 'D',
        name: '',
    }
}

const isLoading = ref(false)
const category = ref(newCategory())
const errors = ref({})


const save = () => {
    isLoading.value = true
    //delete errors.value

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    axios.post('categories', category.value)
        .then(() => {
            toast.success('Category created')
            isLoading.value = false
            router.push({ name: 'categories' })
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }
            isLoading.value = false
            toast.error('Error creating category')
        })
        .finally(() => {
            isLoading.value = false
        })

}

const validateInsert = () => {
    let isValid = true

    if (!category.value.name || category.value.name.trim() === '') {
        errors.value.name = ['Name is required']
        isValid = false

    } else if (category.value.name.length > 50) {
        errors.value.name = ['Name must not exceed 50 characters']
        isValid = false
    }

    if (!category.value.type || !['C', 'D'].includes(category.value.type)) {
        errors.value.type = ['Type is required and must be either "Credit" or "Debit"']
        isValid = false
    }

    return isValid
}

const cancel = () => {
    router.push({ name: 'categories' })
}

</script>

<template>
    <category-detail :is-parent-loading="isLoading" :category="category" :errors="errors"
        @save="save" @cancel="cancel" />
</template>

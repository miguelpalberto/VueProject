<script setup>
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import DefaultCatDetail from './DefaultCatDetail.vue'
import { useDefaultCatStore } from '../../stores/defaultCategory'

const router = useRouter()
const toast = useToast()
const defaultCatStore = useDefaultCatStore()

const newCategory = () => {
    return {
        id: null,
        type: 'D',
        name: '',
    }
}

const isLoading = ref(false)
const category = ref(newCategory())
const errors = ref({})

const save = async () => {
    isLoading.value = true

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    try {
        await defaultCatStore.insertCategory(category.value)
            .then(() => {
                toast.success('Default Category created')
                router.push({ name: 'defaultCategories' })
            })
    }
    catch (error) {
        if (error.response.status === 422) {
            errors.value = error.response.data.errors
        }
        toast.error('Error creating Default Category')
    }
    finally {
        isLoading.value = false
    }
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
    router.push({ name: 'defaultCategories' })
}

</script>
<template>
    <default-cat-detail :is-parent-loading="isLoading" :category="category" :errors="errors" @save="save"
        @cancel="cancel" />
</template>

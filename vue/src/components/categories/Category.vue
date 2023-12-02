<script setup>
import axios from 'axios'
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useToast } from 'vue-toastification'
import CategoryDetail from './CategoryDetail.vue'

const newCategory = () => {
    return {
        id: null,
        vcard: authStore.user.username,
        type: 'D',
        name: '',
    }
}

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()


const isLoading = ref(false)
const category = ref(newCategory())
const errors = ref({})

const props = defineProps({
    id: {
        type: Number,
        default: null
    },
})

const loadCategory = (id) => {
    if (!id || (id < 0)) {
        category.value = newCategory()
    } else {
    axios.get('categories/' + id)
        .then((response) => {
            category.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
        })
    }
}

const save = () => {
    isLoading.value = true
    delete errors.value

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    axios.post('categories', category.value)
    .then(() => {
        insertCategory(category.value)
    })
    .catch((error) => {
        if (error.response.status === 422) {
            generateExternalErrors(error.response.data.message)
        }
        isLoading.value = false
        toast.error('Error creating category')
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
//se watch e computed tiverem chamadas a funcoes dentro, devem estar por baixo (para inicializar o resto antes)
watch(() => props.id,
    (newValue) => {
        loadCategory(newValue)
    }, 
    { 
        immediate: true
    }
)

const operation = computed( () => (!props.id || props.id < 0) ? 'insert' : 'update')

</script>
<template>
    <category-detail
        :is-parent-loading="isLoading"
        :category="category"
        :operationType="operation"
        :errors="errors"
        @save="save"
        @cancel="cancel"
    />
</template>

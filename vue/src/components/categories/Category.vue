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
const props = defineProps({
    id: {
        type: Number,
        default: null
    },
})


const backUrl = computed(() => {
    return props.vcard ? '/vcards/' + props.vcard + '/categories' : '/categories'
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
    //delete errors.value

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    axios.post('categories', category.value)
    .then(() => {
        toast.success('Category created')
            if (!props.isAdmin){
                authStore.loadUser()}
            isLoading.value = false
            router.push({ path: backUrl.value })
    })
    .catch((error) => {
        console.log(error)
         if (error.response.status === 422) {
        //     generateExternalErrors(error.response.data.message)
                errors.value = error.response.data.errors
         }
        isLoading.value = false
        toast.error('Error creating category' )
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

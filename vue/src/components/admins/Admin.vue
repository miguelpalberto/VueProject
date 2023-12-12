<script setup>
import axios from 'axios'
import { ref, computed, watch } from 'vue'
import { useToast } from "vue-toastification";
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import AdminDetail from "./AdminDetail.vue"

const router = useRouter()
const authStore = useAuthStore()
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
const props = defineProps({
    id: {
        type: Number,
        default: null
    }
})

const backUrl = computed(() => {
    return '/users'
})

const loadUser = (id) => {
    if (!id || id < 0) {
        admin.value = newUser()
    } else {
        axios
            .get('users/' + id)
            .then((response) => {
                admin.value = response.data.data
            })
            .catch((error) => {
                console.log(error)
            })
    }
}

const validateInsert = () => {
    let isValid = true
    //console.log('validating')
    if (!admin.value.name || admin.value.name.trim() === '') {
        errors.value.name = ['Name is required']
        isValid = false
        
    } else if (admin.value.name.length > 255) {
        errors.value.name = ['Name must not exceed 255 characters']
        isValid = false
    }

     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    //const emailRegex = ''
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

    console.log('validating')
    return isValid
}

const save = () => {
    isLoading.value = true

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    axios.post('users', admin.value)
        .then((response) => {
            //console.dir(response.data.data)
            isLoading.value = false
            router.push({ path: backUrl.value })
        })
        .catch((error) => {
        console.log(error)
         if (error.response.status === 422) {
                errors.value = error.response.data.errors
         }
        isLoading.value = false
        toast.error('Error creating administrator' )
    })
    .finally(() => {
         isLoading.value = false
     })
}

const cancel = () => {
    // Replace this code to navigate back
    //loadUser(props.id)
    router.push({ name: 'admins' })
}

//const user = ref(newUser())

watch(
    () => props.id,
    (newValue) => {
        loadUser(newValue)
    },
    { immediate: true }
)
</script>

<template>
    <admin-detail 
        :admin="admin" 
        :errors="errors"
        @save="save" 
        @cancel="cancel"
    />
</template>
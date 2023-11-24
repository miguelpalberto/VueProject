<script setup>
import axios from 'axios'
import { ref, computed, onMounted, inject, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import TransactionDetail from "./TransactionDetail.vue"

const props = defineProps({
    vcard: {
        type: String,
        default: null
    },
})

const newTransaction = () => {
    return {
        id: null,
        vcard: null,
        type: 'D',
        value: 0,
        payment_reference: '',
        pair_vcard: null,
        payment_type: null,
        category_id: null,
        description: ''
    }
}

const axiosExternal = inject('axiosExternal')

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const allCategories = ref([])
const transaction = ref(newTransaction())
const errors = ref({})
const isLoading = ref(false)

const allPaymentTypes = [
    'VCARD',
    'MBWAY',
    'PAYPAL',
    'IBAN',
    'MB',
    'VISA'
]

watch(
    () => transaction.value,
    () => {
        transaction.value.type = authStore.isAdmin ? 'C' : 'D'
        transaction.value.vcard = props.vcard ? props.vcard : authStore.user.username
    }
)

const paymentTypes = computed(() => {
    if (!authStore.isAdmin) {
        return allPaymentTypes
    }
    
    return allPaymentTypes.filter((paymentType) => {
        return paymentType != 'VCARD'
    })
})

const vcard = computed(() => {
    return props.vcard ? props.vcard : authStore.user.username
})

const backUrl = computed(() => {
    return props.vcard ? '/vcards/' + props.vcard + '/transactions' : '/transactions'
})

const categories = computed(() => {
    return allCategories.value.filter((category) => {
        return category.type == transaction.value.type
    })
})

const isExternalPaymentType = computed(() => {
    return transaction.value.payment_type != 'VCARD'
})

const externalApiRequest = computed(() => {
    return {
        type: transaction.value.payment_type,
        reference: transaction.value.payment_reference,
        value: transaction.value.value,
    }
})

const loadCategories = () => {
    axios.get('vcards/' + vcard.value + '/categories')
        .then((response) => {
            allCategories.value = response.data.data
        })
        .catch((error) => {
            console.log(error)
            toast.error('Error loading categories')
            //router.push({ name: 'transactions' })
        })
}

const save = () => {
    isLoading.value = true
    delete errors.value

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    if (isExternalPaymentType.value) {
        const externalEndpoint = authStore.isAdmin ? 'credit' : 'debit'
        axiosExternal.post(externalEndpoint, externalApiRequest.value)
            .then(() => {
                insertTransaction(transaction.value)
            })
            .catch((error) => {
                if (error.response.status === 422) {
                    generateExternalErrors(error.response.data.message)
                }
                isLoading.value = false
                toast.error('Error creating transaction')
            })
    }
    else {
        transaction.value.pair_vcard = transaction.value.payment_reference
        insertTransaction(transaction.value)
    }
}

const generateExternalErrors = (message) => {
    const capitalizedMessage = message.charAt(0).toUpperCase() + message.slice(1)
    if (message.includes('type')) {
        errors.value.payment_type = [capitalizedMessage]
    }

    if (message.includes('reference')) {
        errors.value.payment_reference = [capitalizedMessage]
    }

    if (message.includes('value') || message.includes('limit exceeded')) {
        errors.value.value = [capitalizedMessage]
    }
}

const validateInsert = () => {
    let isValid = true

    if (transaction.value.value < 0.01) {
        errors.value.value = ['Value must be greater than 0']
        isValid = false
    }

    if (transaction.value.payment_reference == null || transaction.value.payment_reference == '') {
        errors.value.payment_reference = ['Payment reference is required']
        isValid = false
    }

    if (transaction.value.payment_type == null) {
        errors.value.payment_type = ['Payment type is required']
        isValid = false
    }

    return isValid
}

const insertTransaction = (transaction) => {
    axios.post('transactions', transaction)
        .then(() => {
            toast.success('Transaction created')
            if (!props.isAdmin)
                authStore.loadUser()
            router.push({ path: backUrl.value })
        })
        .catch((error) => {
            if (error.response.status === 422) {
                errors.value = error.response.data.errors
            }

            toast.error('Error creating transaction')
        })
        .finally(() => {
            isLoading.value = false
        })
}


const cancel = () => {
    router.push({ name: 'transactions' })
}

onMounted(() => {
    transaction.value = newTransaction()
    loadCategories()
})


</script>
<template>
    <transaction-detail :is-admin="authStore.isAdmin" :is-parent-loading="isLoading" :transaction="transaction"
        :paymentTypes="paymentTypes" :categories="categories" :errors="errors" @save="save" @cancel="cancel" />
</template>

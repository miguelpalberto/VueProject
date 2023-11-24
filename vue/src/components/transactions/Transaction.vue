<script setup>
import axios from 'axios'
import { ref, computed, onMounted, inject, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import TransactionDetail from "./TransactionDetail.vue"

const props = defineProps({
    id: {
        type: Number,
        default: null
    },
})

const newTransaction = () => {
    return {
        id: null,
        vcard: authStore.user.username,
        type: 'D',
        value: 0,
        payment_reference: '',
        pair_vcard: null,
        payment_type: null,
        category_id: null,
        description: ''
    }
}

const apiUrl = inject('apiUrl')
const externalApiUrl = inject('externalApiUrl')

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const categories = ref([])
const transaction = ref(newTransaction())
const errors = ref({})
const isLoading = ref(false)

const paymentTypes = [
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
    }
)

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

const operation = computed(() => (!props.id || props.id < 0) ? 'insert' : 'update')


const loadTransaction = (id) => {
    if (!id || (id < 0)) {
        transaction.value = newTransaction()
    } else {
        axios.get('transactions/' + id)
            .then((response) => {
                transaction.value = response.data.data
            })
            .catch((error) => {
                console.log(error)
                toast.error('Error loading transaction')
                // router.push({ name: 'transactions' })
            })
    }
}

const loadCategories = () => {
    axios.get('vcards/' + authStore.user.username + '/categories')
        .then((response) => {
            categories.value = response.data.data
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
    if (operation.value == 'insert') {

        if (transaction.value.value < 0.01) {
            errors.value = { 'value': ['Value must be greater than 0'] }
            isLoading.value = false
            return
        }

        if (isExternalPaymentType.value) {
            axios.defaults.baseURL = externalApiUrl
            axios.post('debit', externalApiRequest.value)
                .then(() => {
                    axios.defaults.baseURL = apiUrl
                    insertTransaction(transaction.value)
                })
                .catch((error) => {
                    console.dir(error)
                    axios.defaults.baseURL = apiUrl
                    isLoading.value = false
                    toast.error('Error creating transaction')
                })
        }
        else {
            console.log(transaction.value)
            transaction.value.pair_vcard = transaction.value.payment_reference
            insertTransaction(transaction.value)
        }
    } else {
        axios.put('transactions/' + props.id, transaction.value.category_id)
            .then((response) => {
                console.dir(response.data.data)
            })
            .catch((error) => {
                console.dir(error)
                toast.error('Error updating transaction')
            })
            .finally(() => {
                isLoading.value = false
            })
    }
}

const insertTransaction = (transaction) => {
    axios.post('transactions', transaction)
        .then(() => {
            toast.success('Transaction created')
            router.push({ name: 'transactions' })
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
    loadCategories()
    loadTransaction(props.id)
})


</script>
<template>
    <h2>
        Total Balance: {{ authStore.user.balance }}
    </h2>
    <transaction-detail :operationType="operation" :is-parent-loading="isLoading" :transaction="transaction"
        :paymentTypes="paymentTypes" :categories="categories" :errors="errors" @save="save" @cancel="cancel" />
</template>

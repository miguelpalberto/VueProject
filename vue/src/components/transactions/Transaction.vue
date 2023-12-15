<script setup>
import { ref, computed, onMounted, inject, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import { useTransactionStore } from '../../stores/transaction';
import { useCategoryStore } from '../../stores/category'
import TransactionDetail from "./TransactionDetail.vue"

const props = defineProps({
    vcard: {
        type: String,
        default: null
    }
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
        description: '',
        confirmation_code: undefined,
    }
}

const socket = inject('socket')
const transactionStore = useTransactionStore()
const categoryStore = useCategoryStore()
const authStore = useAuthStore()
const router = useRouter()
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

const categories = computed(() => {
    return allCategories.value.filter((category) => {
        return category.type == transaction.value.type
    })
})


const save = async () => {
    isLoading.value = true
    delete errors.value

    if (!validateInsert()) {
        isLoading.value = false
        return
    }

    try {
        if (transaction.value.payment_type == 'VCARD') {
            transaction.value.pair_vcard = transaction.value.payment_reference
        }

        await transactionStore.create(transaction.value)
        .then(() => {
            toast.success('Transaction created')
            socket.emit('newTransaction', transaction.value)
            if (!authStore.isAdmin)
                authStore.loadUser()
            router.back()
        })
    }
    catch (error) {
        if (error.response.status === 422) {
            errors.value = error.response.data.errors
        }

        toast.error('Error creating transaction')
    }
    finally{
        isLoading.value = false
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

    if (transaction.value.type == 'D' && transaction.value.confirmation_code == null) {
        errors.value.confirmation_code = ['Confirmation code is required']
        isValid = false
    }

    return isValid
}

const cancel = () => {
    router.back()
}

onMounted(async () => {
    transaction.value = newTransaction()
    if (!authStore.isAdmin) {
        allCategories.value = await categoryStore.getCategories(authStore.user.username)
    }
})
</script>
<template>
    <transaction-detail :is-admin="authStore.isAdmin" :is-parent-loading="isLoading" :transaction="transaction"
        :paymentTypes="paymentTypes" :categories="categories" :errors="errors" @save="save" @cancel="cancel" />
</template>

import axios from 'axios'
import { ref, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'

export const useTransactionStore = defineStore('transaction', () => {
    const socket = inject('socket')
    const paginatedTransactions = ref([])
    const toast = useToast()

    const paymentTypes = [
        { value: null, text: 'All' },
        { value: 'VCARD', text: 'VCARD' },
        { value: 'MBWAY', text: 'MBWAY' },
        { value: 'PAYPAL', text: 'PAYPAL' },
        { value: 'IBAN', text: 'IBAN' },
        { value: 'MB', text: 'MB' },
        { value: 'VISA', text: 'VISA' }
    ]

    const types = [
        { value: null, text: 'All' },
        { value: 'C', text: 'Credit Only' },
        { value: 'D', text: 'Debit Only' }
    ]

    const searchValue = ref(null)
    const selectedDate = ref(null)
    const selectedPaymentType = ref(paymentTypes[0].value)
    const selectedType = ref(types[0].value)
    const selectedCategory = ref(null)

    //async of loadVCards
    const load = async (vCardId, page = 1) => {
        const params = {
            page: page
        }

        if (selectedPaymentType.value && paymentTypes.some((s) => s.value === selectedPaymentType.value)) {
            params.paymentType = selectedPaymentType.value
        }

        if (selectedType.value && types.some((s) => s.value === selectedType.value)) {
            params.type = selectedType.value
        }

        if (selectedCategory.value) {
            params.category = selectedCategory.value
        }

        if (selectedDate.value) {
            params.date = selectedDate.value
        }

        if (searchValue.value) {
            params.search = searchValue.value
        }

        const response = await axios.get(`vcards/${vCardId}/transactions`, { params });
        paginatedTransactions.value = response.data;
    }

    const computeQueryPage = () => {
        if (paginatedTransactions.value.current_page == 1) {
            return 1;
        }

        return paginatedTransactions.value.data.length == 1 ? paginatedTransactions.value.current_page - 1 : paginatedTransactions.value.current_page
    }

    return { searchValue, selectedDate, selectedPaymentType, selectedType,
             selectedCategory, paymentTypes, types, paginatedTransactions,
             load };
})
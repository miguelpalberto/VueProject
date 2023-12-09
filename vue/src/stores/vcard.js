import axios from 'axios'
import { ref, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'

export const useVCardStore = defineStore('vcard', () => {
    const socket = inject('socket')
    const paginatedVCards = ref([])
    const toast = useToast()

    const statuses = [
        { value: 'all', text: 'All' },
        { value: 'blockedOnly', text: 'Blocked only' },
        { value: 'unblockedOnly', text: 'Unblocked only' }
    ]

    const searchValue = ref(null)
    const selectedStatus = ref(statuses[0].value)

    //async of loadVCards
    const load = async (page = 1) => {
        const params = {
            page: page
        }

        if (selectedStatus.value && selectedStatus.value !== 'all' && statuses.some((s) => s.value === selectedStatus.value)) {
            params.status = selectedStatus.value
        }

        //check if searchValue is not null and not empty
        if (searchValue.value) {
            params.search = searchValue.value
        }

        const response = await axios.get("vcards", { params });
        paginatedVCards.value = response.data;
    }

    const remove = async (vCard) => {
        await axios.delete('vcards/' + vCard.phone_number)
            .then(async () => {
                const publishUser = {
                    username: vCard.phone_number,
                    isAdmin: false
                }
                socket.emit('userDeleted', publishUser)
                await load(computeQueryPage())
            })
    }

    const block = async (vCard) => {
        await axios.patch('vcards/' + vCard.phone_number + '/block')
            .then(async () => {
                updateStatus(vCard, true)
                socket.emit('vCardBlocked', vCard)
            })
    }

    const unblock = async (vCard) => {
        await axios.patch('vcards/' + vCard.phone_number + '/unblock')
            .then(async () => {
                updateStatus(vCard, false)
                socket.emit('vCardUnblocked', vCard)
            })
    }

    const updateStatus = async (vCard, isBlocked) => {
        const idx = paginatedVCards.value.data.findIndex((t) => t.phone_number === vCard.phone_number)
        if (idx >= 0) {
            paginatedVCards.value.data[idx].blocked = isBlocked
        }
    }

    const updateMaxDebit = async (vCard, maxDebit) => {
        maxDebit = maxDebit.toFixed(2)
        await axios.patch('vcards/' + vCard.phone_number + '/changeMaxDebit', { max_debit: maxDebit })
            .then(() => {
                const idx = paginatedVCards.value.data.findIndex((t) => t.phone_number === vCard.phone_number)
                if (idx >= 0) {
                    paginatedVCards.value.data[idx].max_debit = maxDebit
                }
                socket.emit('vcardMaxDebitChanged', vCard)
            })

    }

    const resetValues = () => {
        searchValue.value = null
        selectedStatus.value = statuses[0].value
        paginatedVCards.value = []
    }

    socket.on('vCardBlocked', (vCard) => {
        const idx = paginatedVCards.value.data.findIndex((t) => t.phone_number === vCard.phone_number)
        toast.info('vCard ' + vCard.phone_number + ' has been blocked.')
        if (idx >= 0) {
            paginatedVCards.value.data[idx].blocked = true
        }
    })

    socket.on('vCardUnblocked', (vCard) => {
        const idx = paginatedVCards.value.data.findIndex((t) => t.phone_number === vCard.phone_number)
        toast.info('vCard ' + vCard.phone_number + ' has been unblocked.')
        if (idx >= 0) {
            paginatedVCards.value.data[idx].blocked = false
        }
    })

    socket.on('userDeleted', async (deletedUser) => {
        if (!deletedUser.isAdmin) {
            toast.info('vCard ' + deletedUser.username + ' has been deleted.')
            const idx = paginatedVCards.value.data.findIndex((v) => v.phone_number === deletedUser.username)
            if (idx >= 0) {
                await load(computeQueryPage())
            }
        }
    })

    socket.on('vcardMaxDebitChanged', async (vCard) => {
        const idx = paginatedVCards.value.data.findIndex((t) => t.phone_number === vCard.phone_number)
        toast.info('vCard ' + vCard.phone_number + ' max debit has been changed.')
        if (idx >= 0) {
            paginatedVCards.value.data[idx].max_debit = vCard.max_debit
        }
    })

    const computeQueryPage = () => {
        if (paginatedVCards.value.current_page == 1) {
            return 1;
        }

        return paginatedVCards.value.data.length == 1 ? paginatedVCards.value.current_page - 1 : paginatedVCards.value.current_page
    }

    return { statuses, selectedStatus, searchValue, paginatedVCards, load, remove, block, unblock, updateMaxDebit, resetValues };
})

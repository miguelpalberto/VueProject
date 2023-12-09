import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'

export const useVCardStore = defineStore('vcard', () => {
    //use paginatedVCards.value.data to access the vcards
    const socket = inject('socket')
    const paginatedVCards = ref([])
    const vCards = computed(() => paginatedVCards.value.data ?? [])
    const toast = useToast()

    const statuses = [
        { value: 'all', text: 'All' },
        { value: 'blockedOnly', text: 'Blocked only' },
        { value: 'unblockedOnly', text: 'Unblocked only' }
    ]

    //async of loadVCards
    const load = async (page = 1, searchValue = null, selectedStatus = { value: 'all' }) => {
        const params = {
            page: page
        }

        if (selectedStatus && selectedStatus !== 'all' && statuses.some((s) => s.value === selectedStatus)) {
            params.status = selectedStatus
        }

        //check if searchValue is not null and not empty
        if (searchValue) {
            params.search = searchValue
        }

        const response = await axios.get("vcards", { params });
        paginatedVCards.value = response.data;
    }

    const remove = async (vCard) => {
        await axios.delete('vcards/' + vCard.phone_number)
            .then(async () => {
                const newPage = paginatedVCards.value.data.length == 1 ? paginatedVCards.value.current_page - 1 : paginatedVCards.value.current_page
                await load(newPage)
            })
    }

    const block = async (vCard) => {
        await axios.patch('vcards/' + vCard.phone_number + '/block')
        socket.emit('vCardBlocked', vCard)
        await load(paginatedVCards.value.current_page)
    }

    const unblock = async (vCard) => {
        await axios.patch('vcards/' + vCard.phone_number + '/unblock')
        await load(paginatedVCards.value.current_page)
    }

    
    socket.on('vCardBlocked', async (vCard) => {
        const idx = paginatedVCards.value.data.findIndex((t) => t.phone_number === vCard.phone_number)
        if (idx >= 0) {
            paginatedVCards.value.data[idx].blocked = true
            toast.info('vCard ' + vCard.phone_number + ' has been blocked.')
        }
    })

    return { statuses, paginatedVCards, load, remove, block, unblock };
})

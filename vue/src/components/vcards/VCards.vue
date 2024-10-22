<script setup>
import { ref, onMounted } from 'vue'
import VCardTable from './VCardTable.vue'
import { useToast } from 'vue-toastification'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'
import { useVCardStore } from '../../stores/vcard'

const vCardStore = useVCardStore()

const isLoading = ref(false)
const toast = useToast()

const loadVCards = async (page = 1) => {
    isLoading.value = true
    try {
        await vCardStore.load(page)
    } catch (error) {
        toast.error('Error loading vCards. Please try again.')
    } finally {
        isLoading.value = false
    }
}

const search = (value) => {
    vCardStore.searchValue = value
    loadVCards(1)
}

const deleteVCard = async (vCard) => {
    try {
        isLoading.value = true
        await vCardStore.remove(vCard)
        toast.success('VCard deleted')
    } catch (error) {
        toast.error('Error deleting vCard. Please try again.')
    } finally {
        isLoading.value = false
    }
}

const blockVCard = async (vCard) => {
    try {
        isLoading.value = true
        await vCardStore.block(vCard)
        toast.success('VCard blocked')
    } catch (error) {
        toast.error('Error blocking vCard. Please try again.')
    } finally {
        isLoading.value = false
    }
}

const unblockVCard = async (vCard) => {
    try {
        isLoading.value = true
        await vCardStore.unblock(vCard)
        toast.success('VCard unblocked')
    } catch (error) {
        toast.error('Error unblocking vCard. Please try again.')
    } finally {
        isLoading.value = false
    }
}

const updateMaxDebit = async (vcard, maxDebit) => {
    try {
        isLoading.value = true
        await vCardStore.updateMaxDebit(vcard, maxDebit)
        toast.success('Max debit updated')
    } catch (error) {
        if (error.response.status == 422) {
            toast.error(error.response.data.errors.max_debit[0])
        } else {
            toast.error('Error updating max debit. Please try again.')
        }
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    vCardStore.resetValues()
    loadVCards()
})
</script>

<template>
    <h3 class="mb-3">VCards</h3>
    <hr />
    <div class="mb-1 row">
        <div class="col-xs-12 col-md-9">
            <label for="inputSearch" class="form-label">Search</label>
            <input
                id="inputSearch"
                class="form-control"
                v-debounce:300ms="search"
                type="text"
                placeholder="Search by phone number, name or email"
                aria-label="Search"
                style="font-size: 14px"
            />
        </div>
        <div class="col-xs-12 col-md-3">
            <label for="inputSearch" style="font-size: 14px" class="form-label">Status</label>
            <select
                id="inputStatus"
                style="font-size: 14px"
                v-model="vCardStore.selectedStatus"
                class="form-select"
                @change="loadVCards()"
            >
                <option
                    v-for="status in vCardStore.statuses"
                    :key="status.value"
                    :value="status.value"
                >
                    {{ status.text }}
                </option>
            </select>
        </div>
    </div>
    <v-card-table
        :is-parent-loading="isLoading"
        :v-cards="vCardStore.paginatedVCards.data"
        @delete="deleteVCard"
        @block="blockVCard"
        @unblock="unblockVCard"
        @update-max-debit="updateMaxDebit"
    />
    <Bootstrap5Pagination
        :data="vCardStore.paginatedVCards"
        @pagination-change-page="loadVCards"
        :limit="1"
        :keepLength="true"
    />
</template>

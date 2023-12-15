<script setup>
import { ref, onMounted } from 'vue'
import AdminTable from './AdminTable.vue'
import { useToast } from 'vue-toastification'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'
import { useAdminStore } from '../../stores/admin'

const toast = useToast()
const adminStore = useAdminStore()
const isLoading = ref(false)

const search = async (value) => {
    adminStore.searchValue = value
    await loadUsers(1)
}

const loadUsers = async (page = 1) => {
    isLoading.value = true
    try {
        await adminStore.load(page)
    }
    catch (error) {
        toast.error('Error loading admins. Please try again.')
    }
    finally {
        isLoading.value = false
    }
}

const deleteUser = (user) => {
    try {
        isLoading.value = true
        adminStore.remove(user)
        toast.success('Admin deleted')
    }
    catch (error) {
        toast.error('Error deleting admin. Please try again.')
    }
    finally {
        isLoading.value = false
    }
}

onMounted(async () => {
    await loadUsers()
})
</script>

<template>
    <h3 class="mb-3">Administrators</h3>
    <hr />
    <router-link class="btn btn-success btn-sm" to="/users/create">
        <i class="bi bi-send-plus"></i>
        <span>New Administrator Account</span>
    </router-link>

    <hr>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-12 mx-auto">
                <div class="mb-1">
                    <input class="form-control" v-debounce:300ms="search" type="text" placeholder="Search"
                        aria-label="Search" />
                </div>
                <admin-table :users="adminStore.paginatedAdmins.data" @delete="deleteUser"></admin-table>
                <Bootstrap5Pagination :data="adminStore.paginatedAdmins" @pagination-change-page="loadUsers" :limit="1"
                    :keepLength="true" />
            </div>
        </div>
    </div>
</template>

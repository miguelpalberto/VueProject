<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import AdminTable from './AdminTable.vue'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

const router = useRouter()
const toast = useToast()

const users = ref([])

const loadUsers = (page = 1, searchValue = null) => {

    const params = {
        page: page
    }

    if (searchValue) {
        params.search = searchValue
    }

    axios
        .get('users', { params })
        .then((response) => {
            users.value = response.data
        })
        .catch((error) => {
            console.log(error)
        })
}

const search = (value) => {
    loadUsers(1, value)
}


const deleteUser = (user) => {
    //if (confirm('Are you sure you want to delete this user?')) {

        const response = axios
            .delete('users/' + user.id)
            .then((response) => {
                toast.success('User deleted')
                loadUsers()
            })
            .catch((error) => {
                console.log(error)
                toast.error('Error deleting user')
            })
    //}
}

onMounted(() => {
    loadUsers()
})
</script>

<template>
    <h3 class="mt-5 mb-3">Admins</h3>
    <hr />
    <div class="mb-1">
        <input class="form-control" v-debounce:300ms="search" type="text" placeholder="Search" aria-label="Search" />
    </div>
    <admin-table :users="users.data" :showId="false" @boolDeleteAdminConfirmed="deleteUser"></admin-table>
    <Bootstrap5Pagination :data="users" @pagination-change-page="loadUsers" />
</template>

<style scoped>
.filter-div {
    min-width: 12rem;
}

.total-filtro {
    margin-top: 2.3rem;
}
</style>

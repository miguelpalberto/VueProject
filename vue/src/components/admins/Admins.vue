<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import AdminTable from './AdminTable.vue'
import { useToast } from 'vue-toastification'

import { useRouter } from 'vue-router'

const router = useRouter()
const toast = useToast()

const users = ref([])

const loadUsers = () => {
    axios
        .get('users')
        .then((response) => {
            users.value = response.data
        })
        .catch((error) => {
            console.log(error)
        })
}

const deleteUser = (user) => {
    if (confirm('Are you sure you want to delete this user?')) {
        axios
            .delete('users/' + user.id)
            .then((response) => {
                toast.success('User deleted')
                loadUsers()
            })
            .catch((error) => {
                console.log(error)
                toast.error('Error deleting user')
            })
    }
}

onMounted(() => {
    loadUsers()
})
</script>

<template>
    <h3 class="mt-5 mb-3">Admins</h3>
    <hr />
    <admin-table :users="users" :showId="false" @delete="deleteUser"></admin-table>
</template>

<style scoped>
.filter-div {
    min-width: 12rem;
}

.total-filtro {
    margin-top: 2.3rem;
}
</style>

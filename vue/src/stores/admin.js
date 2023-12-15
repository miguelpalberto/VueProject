import axios from 'axios'
import { ref, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'
import { useAuthStore } from './auth'

export const useAdminStore = defineStore('admin', () => {
    const socket = inject('socket')
    const toast = useToast()
    const authStore = useAuthStore()

    const paginatedAdmins = ref([])
    const searchValue = ref(null)

    const load = async (page = 1) => {
        const params = {
            page: page
        }

        if (searchValue.value) {
            params.search = searchValue.value
        }

        const response = await axios.get("users", { params });
        paginatedAdmins.value = response.data;
    }

    const remove = async (user) => {
        await axios.delete('users/' + user.id)
            .then(async () => {
                const publishUser = {
                    username: user.email,
                    isAdmin: true
                }
                socket.emit('userDeleted', publishUser)
                await load(computeQueryPage())
            })
    }

    socket.on('userDeleted', async (deletedUser) => {
        if (deletedUser.isAdmin && (authStore.isAuthenticated && authStore.user.username != deletedUser.username)) {
            toast.info('admin ' + deletedUser.username + ' has been deleted.')
            const idx = paginatedAdmins.value.data.findIndex((u) => u.email === deletedUser.username)
            if (idx >= 0) {
                await load(computeQueryPage())
            }
        }
    })

    socket.on('adminProfileUpdated', (user) => {
        const idx = paginatedAdmins.value.data.findIndex((t) => t.id === user.id)
        if (idx >= 0) {
            toast.info('Admin ' + user.username + ' profile has been changed.')
            paginatedAdmins.value.data[idx].name = user.name
            paginatedAdmins.value.data[idx].email = user.email
        }
    })

    const computeQueryPage = () => {
        if (paginatedAdmins.value.current_page == 1) {
            return 1;
        }

        return paginatedAdmins.value.data.length == 1 ? paginatedAdmins.value.current_page - 1 : paginatedAdmins.value.current_page
    }

    const resetValues = () => {
        searchValue.value = null
        paginatedAdmins.value = []
    }

    return { paginatedAdmins, searchValue, load, remove, resetValues };
})
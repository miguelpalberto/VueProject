import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useUserStore = defineStore('user', () => {
    const serverUrl = inject('serverUrl')

    const user = ref(null)
    const userName = computed(() => user.value?.name ?? 'Anonymous')

    const userPhotoUrl = computed(() =>
        user.value?.photo_url ? serverUrl + '/storage/fotos/' + user.value.photo_url : avatarNoneUrl
    )
    async function loadUser() {
        try {
            const response = await axios.get('users/me')
            user.value = response.data.data
        } catch (error) {
            clearUser()
            throw error
        }
    }
    function clearUser() {
        user.value = null
    }
    return { user, userName, userPhotoUrl, loadUser, clearUser }
})

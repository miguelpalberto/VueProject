import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from "@/assets/avatar-none.png";
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
    const serverUrl = inject("serverUrl");
    const user = ref(null)
    const router = useRouter()
    const isAuthenticated = computed(() => !!user.value)
    const userName = computed(() => user.value?.name ?? "Anonymous")
    const isAdmin = computed(() => user.value?.isAdmin ?? false)
    const userPhotoUrl = computed(() =>
        user.value?.photo_url
            ? serverUrl + "/storage/fotos/" + user.value.photo_url
            : avatarNoneUrl
    );

    const loadUser = async () => {
        try {
            const response = await axios.get("authUsers/me");
            user.value = response.data.data;
        } catch (error) {
            clearUser();
            throw error;
        }
    }

    const updateProfile = async (data) => {
        await axios.put("authUsers/me", data)
        user.value.name = data.name
        user.value.email = data.email
    }

    const logout = async () => {
        try {
            await axios.post('/auth/logout')
        } catch (error) {
            console.log(error)
        } finally {
            clearUser()
            delete axios.defaults.headers.common.Authorization
            sessionStorage.removeItem('token')
            router.push({ name: 'login' })
        }
    }

    function clearUser() {
        user.value = null;
    }
    return { isAuthenticated, isAdmin, user, userName, userPhotoUrl, loadUser, clearUser, updateProfile, logout };
})

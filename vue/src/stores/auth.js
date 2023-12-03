import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from "@/assets/avatar-none.png";

export const useAuthStore = defineStore('auth', () => {
    const serverUrl = inject("serverUrl");
    const user = ref(null)
    const isAuthenticated = computed(() => !!user.value)
    const userName = computed(() => user.value?.name ?? "Anonymous")
    const isAdmin = computed(() => user.value?.isAdmin ?? false)
    const userPhotoUrl = computed(() =>
        user.value?.photo_url
            ? serverUrl + "/storage/fotos/" + user.value.photo_url
            : avatarNoneUrl
    );

    async function loadUser() {
        try {
            const response = await axios.get("authUsers/me");
            user.value = response.data.data;
        } catch (error) {
            clearUser();
            throw error;
        }
    }
    
    function clearUser() {
        user.value = null;
    }
    return { isAuthenticated, isAdmin, user, userName, userPhotoUrl, loadUser, clearUser };
})

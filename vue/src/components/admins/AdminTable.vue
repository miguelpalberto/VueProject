<script setup>
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const canViewUserDetail = (email) => {
    if (!authStore.user) {
        return false
    }
    return authStore.user.email == email
}

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    },
    showId: {
        type: Boolean,
        default: true
    },
    showEmail: {
        type: Boolean,
        default: true
    },
    showCreatedAt: {
        type: Boolean,
        default: true
    },
    showDeleteButton: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['delete'])

const deleteClick = (user) => {
    emit('delete', user)
}
</script>

<template>
    <table class="table">
        <thead>
            <tr>
                <th v-if="showId" class="align-middle">#</th>
                <th class="align-middle">Name</th>
                <th v-if="showEmail" class="align-middle">Email</th>
                <th v-if="showCreatedAt" class="align-middle">Admin Since</th>
                <th v-if="showDeleteButton" class="align-middle">Delete</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="user in props.users" :key="user.email">
                <td v-if="showId" class="align-middle">{{ user.id }}</td>
                <td class="align-middle">{{ user.name }}</td>
                <td v-if="showEmail" class="align-middle">{{ user.email }}</td>
                <td v-if="showCreatedAt" class="align-middle">{{ user.created_at }}</td>
                <td class="text-end align-middle" v-if="showDeleteButton">
                    <div class="d-flex justify-content-end">
                        <button
                            class="btn btn-xs btn-light"
                            @click="deleteClick(user)"
                            v-if="showDeleteButton"
                        >
                            <i class="bi bi-xs bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<style scoped>
button {
    margin-left: 3px;
    margin-right: 3px;
}
</style>

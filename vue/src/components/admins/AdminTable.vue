<script setup>
import { useAuthStore } from '../../stores/auth'
import { ref, computed } from 'vue'

const authStore = useAuthStore()

const canDelete = (email) => {
    if (!authStore.user) {
        return false
    }
    return authStore.user.email != email
}

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    },
})

const emit = defineEmits(['delete'])
const deleteConfirmationDialog = ref(null)
const selectedUser = ref(null)

const modalMessage = computed(() => {
    if (!selectedUser.value) {
        return ''
    }
    return `Are you sure you want to delete <b>${selectedUser.value.name}</b>?`
})

const deleteClick = (user) => {
    selectedUser.value = user   
    deleteConfirmationDialog.value.show() 
}
const deleteConfirmed = (isConfirmed) => {
    if (isConfirmed) {
        emit('delete', selectedUser.value)
    }
    selectedUser.value = null
}
</script>

<template>
    <confirmation-dialog
    ref="deleteConfirmationDialog"
    confirmationBtn="Delete administrator"
    :msg="modalMessage"
    @response="deleteConfirmed"
>
</confirmation-dialog>
    <table class="table">
        <thead>
            <tr>
                <th class="align-middle">Name</th>
                <th class="align-middle">Email</th>
                <th class="align-middle">Admin Since</th>
                <th class="align-middle"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="props.users.length === 0">
                <td colspan="4" class="text-center">No users found</td>
            </tr>
            <tr v-else v-for="user in props.users" :key="user.email">
                <td class="align-middle">{{ user.name }}</td>
                <td class="align-middle">{{ user.email }}</td>
                <td class="align-middle">{{ user.created_at }}</td>
                <td class="text-end align-middle">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-xs btn-light" @click="deleteClick(user)"
                            v-if="(canDelete(user.email))">
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

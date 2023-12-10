<script setup>
import { inject, ref } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'
import { setMask, removeMask, formatToNumber } from 'simple-mask-money'

const serverUrl = inject("serverUrl");

const props = defineProps({
    isParentLoading: {
        type: Boolean,
        default: false
    },
    vCards: {
        type: Array,
        default: () => []
    },
})

const emit = defineEmits(['delete', 'block', 'unblock', 'updateMaxDebit'])

const confirmationDialogRef = ref(null)
const modalMessage = ref('');
const modalButtonText = ref('');
const currentAction = ref('')
const selectedVCard = ref(null)
const maxDebitOfSelectedVCard = ref(null)
const isEditing = ref(false)
const isVCardSelected = (vCard) => {
    if (!selectedVCard.value) {
        return false
    }
    return selectedVCard.value.phone_number == vCard.phone_number
}

const stopEditing = () => {
    isEditing.value = false
    selectedVCard.value = null
    maxDebitOfSelectedVCard.value = null
    removeMask('#inputMaxDebit')
}

const startEditing = (vCard) => {
    isEditing.value = true
    selectedVCard.value = vCard
    maxDebitOfSelectedVCard.value = vCard.max_debit
    setTimeout(() => {
        setMask('#inputMaxDebit', {
            suffix: '€',
            fixed: true,
            fractionDigits: 2,
            decimalSeparator: ',',
            thousandsSeparator: '.',
            cursor: 'end'
        })
    }, 100)
}

const toggleEditingVCard = (vCard) => {
    if (isEditing.value) {
        stopEditing()
    } else {
        startEditing(vCard)
    }
}

const photoFullUrl = (vCard) => {
    return vCard.photo_url
        ? serverUrl + "/storage/fotos/" + vCard.photo_url
        : avatarNoneUrl;
};

const isVCardAllowedToBeDeleted = (vCard) => {
    return vCard.balance == 0;
};

const handleConfirmation = (isConfirmed) => {
    if (isConfirmed) {
        switch (currentAction.value) {
            case 'delete':
                handleDeleteConfirmed(selectedVCard.value)
                break;
            case 'block':
                handleBlockConfirmed(selectedVCard.value)
                break;
            case 'unblock':
                handleUnblockConfirmed(selectedVCard.value)
                break;
            case 'updateMaxDebit':
                updateMaxDebitConfirmed(selectedVCard.value)
                break;
            default:
                stopEditing()
                break;
        }
    }

    stopEditing()
}

const handleDelete = (vCard) => {
    selectedVCard.value = vCard
    modalMessage.value = `Are you sure you want to delete <b>${vCard.name}'s</b> vCard?`
    modalButtonText.value = 'Delete'
    currentAction.value = 'delete'
    confirmationDialogRef.value.show()
}

const handleDeleteConfirmed = () => {
    emit('delete', selectedVCard.value)
}

const handleBlock = (vCard) => {
    selectedVCard.value = vCard
    modalMessage.value = `Are you sure you want to block <b>${vCard.name}'s</b> vCard?`
    modalButtonText.value = 'Block'
    currentAction.value = 'block'
    confirmationDialogRef.value.show()
}

const handleBlockConfirmed = (vCard) => {
    emit('block', vCard)
}

const handleUnblock = (vCard) => {
    selectedVCard.value = vCard
    modalMessage.value = `Are you sure you want to unblock <b>${vCard.name}'s</b> vCard?`
    modalButtonText.value = 'Unblock'
    currentAction.value = 'unblock'
    confirmationDialogRef.value.show()
}

const handleUnblockConfirmed = (vCard) => {
    emit('unblock', vCard)
}

const updateMaxDebit = () => {
    modalMessage.value = `Are you sure you want to update the max debit of <b>${selectedVCard.value.name}'s</b> vCard?`
    modalButtonText.value = 'Update'
    currentAction.value = 'updateMaxDebit'
    confirmationDialogRef.value.show()
}

const updateMaxDebitConfirmed = () => {
    emit('updateMaxDebit', selectedVCard.value, formatToNumber(maxDebitOfSelectedVCard.value))
}

</script>

<template>
    <confirmation-dialog ref="confirmationDialogRef" :confirmationBtn="modalButtonText" :msg="modalMessage" @response="handleConfirmation"/>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th class="align-middle">Phone Number</th>
                    <th class="align-middle">Name</th>
                    <th class="align-middle">Email</th>
                    <th class="align-middle">Balance</th>
                    <th class="align-middle">Debit Limit</th>
                    <th class="align-middle"><!-- Actions --></th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="props.vCards.length === 0">
                    <td colspan="7" class="text-center">No vCards found</td>
                </tr>
                <tr v-else :class="{ 'disabled': isEditing && !isVCardSelected(vCard) }" v-for="vCard in props.vCards"
                    :key="vCard.phone_number">
                    <td class="align-middle">
                        <img :src="photoFullUrl(vCard)" class="rounded-circle img_photo" />
                    </td>
                    <td class="align-middle">{{ vCard.phone_number }}</td>
                    <td class="align-middle">{{ vCard.name }}</td>
                    <td class="align-middle">{{ vCard.email }}</td>
                    <td class="align-middle">{{ vCard.balance }}€</td>
                    <td class="align-middle">
                        <span v-if="(isEditing && !isVCardSelected(vCard)) || !isEditing">
                            {{ vCard.max_debit }}€
                        </span>
                        <div v-else class="d-flex justify-content-center gap-1" style="min-width: 150px;">
                            <input id="inputMaxDebit" :disabled="isParentLoading" type="text" class="form-control"
                                v-model="maxDebitOfSelectedVCard" />
                            <button type="button" class="btn btn-success" @click="updateMaxDebit(vCard)"
                                :disabled="isParentLoading">
                                <i class="bi bi-box-arrow-down"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-end align-middle">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-light" @click="toggleEditingVCard(vCard)"
                                :disabled="isParentLoading || (isEditing && !isVCardSelected(vCard))">
                                <i class="bi" :class="{
                                    'bi-x-circle': ((isEditing && isVCardSelected(vCard))),
                                    'bi-pencil-square': ((isEditing && !isVCardSelected(vCard)) || !isEditing)
                                }">
                                </i>
                            </button>
                            <router-link :to="{ name: 'createvCardTransaction', params: { vcard: vCard.phone_number } }"
                                class="btn btn-xs btn-light" :class="{ disabled: isParentLoading || isEditing }"
                                v-if="!vCard.blocked">
                                <i class="bi bi-send-fill text-success"></i>
                            </router-link>
                            <button
                            class="btn btn-xs btn-light"
                            v-if="vCard.blocked" disabled>
                            <i class="bi bi-send text-secondary"></i>
                            </button>
                            <button class="btn btn-xs btn-light" @click="handleBlock(vCard)"
                                :disabled="isParentLoading || isEditing" v-if="!vCard.blocked">
                                <i class="bi bi-lock-fill text-danger"></i>
                            </button>
                            <button class="btn btn-xs btn-light" @click="handleUnblock(vCard)"
                                :disabled="isParentLoading || isEditing" v-if="vCard.blocked">
                                <i class="bi bi-unlock-fill text-primary"></i>
                            </button>
                            <button class="btn btn-xs btn-light" @click="handleDelete(vCard)"
                                :disabled="isParentLoading || !isVCardAllowedToBeDeleted(vCard) || isEditing">
                                <i class="bi bi-xs bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.img_photo {
    width: 3.2rem;
    height: 3.2rem;
}

.bi {
    margin: 0 !important;
}

.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.5;
}

tr.disabled {
    opacity: 50%;
}
</style>
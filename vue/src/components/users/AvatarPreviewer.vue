<script setup>
import { ref, onMounted, computed } from 'vue'
import avatarNoneUrl from "@/assets/avatar-none.png";
import { useAuthStore } from '../../stores/auth';
import { useToast } from "vue-toastification";
import axios from 'axios'

const authStore = useAuthStore();
const toast = useToast()

const emit = defineEmits(['inputFileChanged'])
const props = defineProps({
    isParentLoading: {
        type: Boolean,
        default: false,
        required: false
    },
    imgUrl: {
        type: String,
        required: true
    },
    allowUploadInComponent: {
        type: Boolean,
        default: false
    },
    allowDelete: {
        type: Boolean,
        default: false
    },
})

const inputFile = ref(null)
const avatarImg = ref(null)
const srcBackup = ref(null)
const canCancel = ref(false)
const isLoading = ref(false)
const errors = ref({})

const canDelete = computed(() => {
    return props.allowDelete && srcBackup.value != window.location.origin + avatarNoneUrl && !canCancel.value
})

const canUpload = computed(() => {
    return props.allowUploadInComponent && canCancel.value
})

const reset = () => {
    avatarImg.value.src = srcBackup.value ?? avatarNoneUrl
    canCancel.value = false
    inputFile.value.value = ''
}

const onInputFileChanged = () => {
    const [file] = inputFile.value.files
    if (file) {
        avatarImg.value.src = URL.createObjectURL(file)
        canCancel.value = true
        if (!props.allowUploadInComponent)
            emit('inputFileChanged', file)
    }
}

const upload = () => {
    if (avatarImg.value.src == avatarNoneUrl) {
        return
    }

    const [file] = inputFile.value.files
    if (file) {
        if (confirm('Are you sure you want to upload this photo?')) {
            isLoading.value = true
            const formData = new FormData()
            formData.append('photo_file', file)

            axios.post(`/vcards/${authStore.user.username}/photo`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(() => {
                    authStore.loadUser()
                    srcBackup.value = avatarImg.value.src
                    canCancel.value = false
                })
                .catch((errors) => {
                    if (errors.response.status === 422) {
                        errors.value = errors.response.data.errors
                    }

                    toast.error('Something went wrong please try again')
                })
                .finally(() => {
                    isLoading.value = false
                })
        }
    }
}

const deletePhoto = () => {
    if (avatarImg.value.src == avatarNoneUrl) {
        return
    }

    if (confirm('Are you sure you want to delete this photo?')) {
        isLoading.value = true
        axios.delete(`/vcards/${authStore.user.username}/photo`)
            .then(() => {
                authStore.loadUser()
                avatarImg.value.src = avatarNoneUrl
                srcBackup.value = window.location.origin + avatarNoneUrl
                inputFile.value.value = ''
            })
            .catch(() => {
                toast.error('Something went wrong please try again')
            })
            .finally(() => {
                isLoading.value = false
            })
    }
}

const fetchPhoto = () => {
    inputFile.value.click()
}

onMounted(() => {
    inputFile.value?.focus()
    avatarImg.value.focus()
    srcBackup.value = avatarImg.value.src
})

</script>
<template>
    <div class="mb-4 d-flex flex-column align-items-center">
        <input ref="inputFile" class="d-none" @change="onInputFileChanged" type="file"
            :disabled="isParentLoading && !props.allowUploadInComponent">
        <button type="button" class="img-thumbnail-wrapper" @click="fetchPhoto" :disabled="isParentLoading">
            <img ref="avatarImg" width="200" height="200"
                style="height: 200px !important; width: 200px !important; margin:0;" :src="props.imgUrl" alt="Avatar"
                class="rounded-circle img-thumbnail">
        </button>
        <div class="mt-2 d-flex flex-row gap-2">
            <button v-if="canCancel" type="button" @click="reset" class="btn btn-dark ml-5">Cancel</button>
            <button v-if="canUpload" type="button" @click="upload" :disabled="isParentLoading || isLoading"
                class="btn btn-success ml-5">
                <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
                <span role="upload">Upload</span>
            </button>
            <button v-if="props.allowDelete && canDelete" :disabled="isParentLoading || isLoading" type="button"
                @click="deletePhoto" class="btn btn-danger">
                <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isLoading"></span>
                <span role="delete">Delete</span>
            </button>
        </div>
      <field-error-message :errors="errors" fieldName="photo_file"></field-error-message>
    </div>
</template>
<style scoped>
button.img-thumbnail-wrapper {
    padding: 0;
    border: 0;
    border-radius: 50%;
    -webkit-box-shadow: 0px 0px 35px -15px rgba(0, 0, 0, 1);
    -moz-box-shadow: 0px 0px 35px -15px rgba(0, 0, 0, 1);
    box-shadow: 0px 0px 35px -15px rgba(0, 0, 0, 1);
}

button.img-thumbnail-wrapper:disabled {
    opacity: 0.5;
}
</style>
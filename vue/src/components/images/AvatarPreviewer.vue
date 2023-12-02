<script setup>
import { ref, onMounted } from 'vue'
import avatarNoneUrl from "@/assets/avatar-none.png";

const inputFile = ref()
const avatarImg = ref()
const showCancelButton = ref(false)
const srcBackup = ref(null)

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
    alt: {
        type: String,
        default: 'Avatar'
    },
    allowUpload: {
        type: Boolean,
        default: false
    },
    allowDelete: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['inputFileChanged'])

const onInputFileChanged = () => {
    const [file] = inputFile.value.files
    if (file) {
        avatarImg.value.src = URL.createObjectURL(file)
        showCancelButton.value = true
        emit('inputFileChanged', file)
    }
}

onMounted(() => {
    inputFile.value.focus()
    avatarImg.value.focus()
    srcBackup.value = avatarImg.value.src
})

const reset = () => {
    avatarImg.value.src = srcBackup.value ?? avatarNoneUrl
    showCancelButton.value = false
    inputFile.value.value = ''
}

</script>
<template>
    <div class="mb-4 d-flex flex-column align-items-center">
        <img ref="avatarImg" width="200" height="200" :src="props.imgUrl" :alt="props.alt"
            class="rounded-circle img-thumbnail">
        <div class="d-flex mt-2" v-if="allowUpload">
            <input ref="inputFile" @change="onInputFileChanged" type="file" :disabled="isParentLoading" class="form-control form-control-sm">
        </div>
        <div class="mt-2 d-flex flex-row gap-2">
            <button v-if="showCancelButton && !isParentLoading" type="button" @click="reset" class="btn btn-dark ml-5">Cancel</button>
            <button v-if="props.allowDelete && imgUrl != avatarNoneUrl && !isParentLoading" type="button" class="btn btn-danger"
                data-bs-toggle="modal" data-bs-target="#confirmationModal">
                Delete Photo
            </button>
        </div>
    </div>
</template>
<script setup>
    defineProps ({
        user: {
            type: Object,
            default: null,
        },
        allowUpload: {
            type: Boolean,
            default: false,
        },
        allowDelete: {
            type: Boolean,
            default: false,
        },
    })
</script>

<template>
<a></a>
    <!-- <div class="mb-4 d-flex flex-column align-items-center">
        <img id="avatarImg" width="200" height="200" src="{{ $user->fullPhotoUrl }}" alt="Avatar"
        class="rounded-circle img-thumbnail">
        @if ($allowUpload)
        <div class="d-flex mt-2">
            <input type="file" class="form-control @error('photo_file') is-invalid @enderror" name="photo_file"
            id="inputFilePhoto">
        @error('photo_file')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
        <button id="btnNewAvatarCancel" type="button" class="btn btn-dark ml-5" style="display:none;">Cancelar</button>
    </div>
    @endif
    @if (($allowDelete ?? false) && $user->photo_url)
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal">
            Apagar Foto
        </button>
    @endif -->
</div>
</template>
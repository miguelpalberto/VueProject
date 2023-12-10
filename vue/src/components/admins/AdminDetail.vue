<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    isParentLoading: {
      type: Boolean,
      required: true,
    },
    administrator: {
      type: Object,
      required: true
    },
    errors: {
      type: Object,
      required: true
    },
    types: {
    type: Array,
    required: true
   },
})


const emit = defineEmits(['save', 'cancel'])

const editingCategory = ref(props.category)

const categoryTitle = computed(() => {
  return 'New Administrator Account'
})

const save = () => {
  emit('save', editingCategory.value)
}

const cancel = () => {
  emit('cancel', editingCategory.value)
}


</script>

<template>

  <h3 class="mb-2">{{ categoryTitle }}</h3>
  <hr>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">

    <div class="mb-1">
      <label for="inputName" class="form-label">Name<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.name }" min="0"
        :disabled="isParentLoading" id="inputName" required v-model="editingAdministrator.name">
      <div class="invalid-feedback" v-if="errors && errors.name">
        {{ errors.name[0] }}
      </div>
    </div>

    <div class="mb-1">
      <label for="inputEmail" class="form-label">Email<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.email }" min="0"
        :disabled="isParentLoading" id="inputEmail" required v-model="editingAdministrator.email">
      <div class="invalid-feedback" v-if="errors && errors.email">
        {{ errors.email[0] }}
      </div>
    </div>

    <div class="mb-1">
      <label for="inputPassword" class="form-label">Password<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.password }" min="0"
        :disabled="isParentLoading" id="inputPassword" required v-model="editingAdministrator.password">
      <div class="invalid-feedback" v-if="errors && errors.password">
        {{ errors.password[0] }}
      </div>
    </div>




    <div class="mb-5 d-flex">
      <button :disabled="isParentLoading" type="button" class="btn btn-primary px-5" @click="save">
        <span class="spinner-border spinner-border-sm mx-1" aria-hidden="true" v-if="isParentLoading"></span>
        <span role="save">Save</span>
      </button>
      <button :disabled="isParentLoading" type="button" class="btn btn-light px-5" @click="cancel">
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    isParentLoading: {
      type: Boolean,
      required: true,
    },
    category: {
      type: Object,
      required: true
    },
    operationType: {
      type: String,
      default: 'insert'  // insert / update
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

watch(
  () => props.category,
  (newCategory) => {
    editingCategory.value = newCategory
  }
)

const categoryTitle = computed(() => {
  if (!editingCategory.value) {
    return ''
  }
      return props.operationType == 'insert' ? 'New VCard Category' : editingCategory.value.name
})

const save = () => {
  emit('save', editingCategory.value)
}

const cancel = () => {
  emit('cancel', editingCategory.value)
}

//  data() => {
//     return {
//       types: ['C', 'D'], // Your original types array
//       editingCategory: {
//         type: null, // Initial value, you can set it based on your requirements
//       },
//       isParentLoading: false, // Assuming you have this variable in your component
//       errors: {
//         payment_type: false, // Assuming you have error handling for payment_type
//       },
//     };
//   },
//   computed: {
//     filteredTypes() {
//       // Filter the types array to only include 'C' and 'D'
//       return this.types.filter(type => ['C', 'D'].includes(type));
//     },
//   }



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
        :disabled="isParentLoading" id="inputName" required v-model="editingCategory.name">
      <div class="invalid-feedback" v-if="errors && errors.name">
        {{ errors.name[0] }}
      </div>
    </div>

    <div class="mb-1">
      <label for="inputType" class="form-label">Type<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>

      <select class="form-select" :class="{ 'is-invalid': errors && errors.payment_type }" :disabled="isParentLoading"
        id="inputType" required v-model="editingCategory.type">
        <!-- <option :value="null" selected>--Choose a Type--</option> -->
              <option :value="'C'">Credit</option>
              <option :value="'D'" selected>Debit</option>
        <!-- <option v-for="type in types" :key="type" :value="type">{{ type }}</option> -->
      </select>




      <div class="invalid-feedback" v-if="errors && errors.type">
        {{ errors.type[0] }}
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

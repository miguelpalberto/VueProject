<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from 'vue'
import { setMask, removeMask, formatToNumber } from 'simple-mask-money'

const props = defineProps({
  transaction: {
    type: Object,
    required: true
  },
  isParentLoading: {
    type: Boolean,
    required: true,
  },
  operationType: {
    type: String,
    default: 'insert'  // insert / update
  },
  paymentTypes: {
    type: Array,
    required: true
  },
  categories: {
    type: Array,
    required: true
  },
  errors: {
    type: Object,
    required: true
  },
})

const emit = defineEmits(['save', 'cancel'])

const editingTransaction = ref(props.transaction)

watch(
  () => props.transaction,
  (newTransaction) => {
    editingTransaction.value = newTransaction
  }
)

const transactionTitle = computed(() => {
  if (!editingTransaction.value) {
    return ''
  }
  return props.operationType == 'insert' ? 'New Transaction' : 'Task #' + editingTransaction.value.payment_reference
})

const save = () => {
  editingTransaction.value.value = formatToNumber(editingTransaction.value.value)
  emit('save', editingTransaction.value)
}

const cancel = () => {
  emit('cancel', editingTransaction.value)
}

onMounted(() => {
  setMask('#inputValue',
    {
      suffix: '€',
      fixed: true,
      fractionDigits: 2,
      decimalSeparator: ',',
      thousandsSeparator: '.',
      cursor: 'move'
    })
})

onBeforeUnmount(() => {
  removeMask('#inputValue')
})


</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{ transactionTitle }}</h3>
    <hr>

    <div class="mb-3">
      <label for="inputPaymentType" class="form-label">Payment Type<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <select class="form-select" :class="{ 'is-invalid': errors && errors.payment_type }" :disabled="isParentLoading"
        id="inputPaymentType" required v-model="editingTransaction.payment_type">
        <option value="" selected>Choose a Payment Type</option>
        <option v-for="paymentType in paymentTypes" :key="paymentType" :value="paymentType">{{ paymentType }}</option>
      </select>
      <div class="invalid-feedback" v-if="errors && errors.payment_type">
        {{ errors.payment_type[0] }}
      </div>
    </div>
    <div class="mb-3">
      <label for="inputPaymentReference" class="form-label">Payment Reference<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.payment_reference }"
        :disabled="isParentLoading" id="inputPaymentReference" required v-model="editingTransaction.payment_reference">
      <div class="invalid-feedback" v-if="errors && errors.payment_reference">
        {{ errors.payment_reference[0] }}
      </div>
    </div>
    <div class="mb-3">
      <label for="inputValue" class="form-label">Value<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.value }" min="0"
        :disabled="isParentLoading" id="inputValue" required v-model="editingTransaction.value">
      <div class="invalid-feedback" v-if="errors && errors.value">
        {{ errors.value[0] }}
      </div>
    </div>
    <div class="mb-3">
      <label for="inputCategory" class="form-label">Category</label>
      <select class="form-select" :class="{ 'is-invalid': errors && errors.category_id }" :disabled="isParentLoading"
        id="inputCategory" required v-model="editingTransaction.category_id">
        <option value="" selected>No Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
      </select>
      <div class="invalid-feedback" v-if="errors && errors.category_id">
        {{ errors.category_id[0] }}
      </div>
    </div>
    <div class="mb-3">
      <label for="inputDescription" class="form-label">Description</label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.description }"
        :disabled="isParentLoading" id="inputDescription" required v-model="editingTransaction.description">
      <div class="invalid-feedback" v-if="errors && errors.description">
        {{ errors.description[0] }}
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
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

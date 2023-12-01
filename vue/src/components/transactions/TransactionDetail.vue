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
  isAdmin: {
    type: Boolean,
    required: true,
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
  return props.isAdmin ? 'New Credit Transaction' : 'New Debit Transaction'
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
      cursor: 'end'
    })
})

onBeforeUnmount(() => {
  removeMask('#inputValue')
})


</script>

<template>
  <h3 class="mb-2">{{ transactionTitle }}</h3>
  <hr>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <div class="mb-1">
      <label for="inputPaymentType" class="form-label">Payment Type<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <select class="form-select" :class="{ 'is-invalid': errors && errors.payment_type }" :disabled="isParentLoading"
        id="inputPaymentType" required v-model="editingTransaction.payment_type">
        <option :value="null" selected>--Choose a Payment Type--</option>
        <option v-for="paymentType in paymentTypes" :key="paymentType" :value="paymentType">{{ paymentType }}</option>
      </select>
      <div class="invalid-feedback" v-if="errors && errors.payment_type">
        {{ errors.payment_type[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputPaymentReference" class="form-label">Payment Reference<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control"
        :class="{ 'is-invalid': errors && errors.payment_reference || errors.pair_vcard }" :disabled="isParentLoading"
        id="inputPaymentReference" required v-model="editingTransaction.payment_reference">
      <div class="invalid-feedback" v-if="errors && errors.payment_reference">
        {{ errors.payment_reference[0] }}
      </div>
      <div class="invalid-feedback" v-if="errors && errors.pair_vcard">
        {{ errors.pair_vcard[0] }}
      </div>
    </div>
    <div class="mb-1">
      <label for="inputValue" class="form-label">Value<span class="text-danger">*</span>
        &nbsp;<span class="text-muted">(required)</span>
      </label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.value }" min="0"
        :disabled="isParentLoading" id="inputValue" required v-model="editingTransaction.value">
      <div class="invalid-feedback" v-if="errors && errors.value">
        {{ errors.value[0] }}
      </div>
    </div>
    <div class="mb-1" v-if="!isAdmin">
      <label for="inputCategory" class="form-label">Category</label>
      <select class="form-select" :class="{ 'is-invalid': errors && errors.category_id }" :disabled="isParentLoading"
        id="inputCategory" required v-model="editingTransaction.category_id">
        <option :value="null" selected>--No Category--</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
      </select>
      <div class="invalid-feedback" v-if="errors && errors.category_id">
        {{ errors.category_id[0] }}
      </div>
    </div>
    <div class="mb-1" v-if="!isAdmin">
      <label for="inputDescription" class="form-label">Description</label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors && errors.description }"
        :disabled="isParentLoading" id="inputDescription" required v-model="editingTransaction.description">
      <div class="invalid-feedback" v-if="errors && errors.description">
        {{ errors.description[0] }}
      </div>
    </div>
    <div class="p-2 rounded-3" v-if="!isAdmin">
      <label for="inputConfirmationCode" class="form-label">
        <h6><i class="bi bi-person-vcard"></i>
          Confirmation Code<span class="text-danger">*</span>
          &nbsp;<span class="text-muted">(required)</span>
        </h6>
      </label>
      <input type="password" class="form-control" :class="{ 'is-invalid': errors && errors.confirmation_code }"
        :disabled="isParentLoading" id="inputConfirmationCode" maxlength="4" required
        v-model="editingTransaction.confirmation_code">
      <div class="invalid-feedback" v-if="errors && errors.confirmation_code">
        {{ errors.confirmation_code[0] }}
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

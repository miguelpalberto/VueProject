<script setup>
import { computed, ref } from "vue";

const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
  isParentLoading: {
    type: Boolean,
    default: false,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(["update"])

const isTransactionSelected = (transaction) => {
  if (!transaction.value) {
    return false
  }

  return selectedTransaction.value.id == transaction.id
}

const selectedTransaction = ref(null)
const confirmationDialogRef = ref(null)
const categoryOfSelectedTransaction = ref(null)
const descriptionOfSelectedTransaction = ref(null)
const isEditing = ref(false)
const selectedTypeCategories = computed(() => {
  if (!selectedTransaction.value) {
    return []
  }

  const collection = [{
    id: null,
    name: "-- No Category --"
  }]

  if (selectedTransaction.value.type === "Debit") {
    collection.push(...props.categories.filter(category => category.type === "D"))
  }
  else if (selectedTransaction.value.type === "Credit") {
    collection.push(...props.categories.filter(category => category.type === "C"))
  }

  return collection;
})

const stopEditing = () => {
  isEditing.value = false
  selectedTransaction.value = null
  categoryOfSelectedTransaction.value = null
  descriptionOfSelectedTransaction.value = null
}

const startEditing = (vCard) => {
  isEditing.value = true
  selectedTransaction.value = vCard
  categoryOfSelectedTransaction.value = vCard.category_id
  descriptionOfSelectedTransaction.value = vCard.description
}

const toggleEditingTransaction = (transaction) => {
  if (isEditing.value) {
    stopEditing()
  } else {
    startEditing(transaction)
  }
}

const updateTransaction = () => {
  confirmationDialogRef.value.show()
}

const updateTransactionConfirmed = (isConfirmed) => {
  if (isConfirmed) {
    emit('update', {
      id: selectedTransaction.value.id,
      category_id: categoryOfSelectedTransaction.value,
      category: categoryOfSelectedTransaction.value ? props.categories.find(c => c.id === categoryOfSelectedTransaction.value).name : null,
      description: descriptionOfSelectedTransaction.value
    })
  }

  stopEditing()
}

</script>
<template>
  <confirmation-dialog ref="confirmationDialogRef" confirmationBtn="Update"
    msg="Are you sure you want to update this transaction?" @response="updateTransactionConfirmed" />
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th class="align-middle">Payment Reference</th>
          <th class="align-middle">Value</th>
          <th class="align-middle">New Balance</th>
          <th class="align-middle">Payment Type</th>
          <th class="align-middle">Date</th>
          <th class="align-middle">Category</th>
          <th class="align-middle">Description</th>
          <th><!--Actions --></th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="transactions.length === 0">
          <td colspan="7" class="text-center">No Transactions found</td>
        </tr>
        <tr :class="{ 'disabled': isEditing && !isTransactionSelected(transaction) }" v-else
          v-for="transaction in props.transactions" :key="transaction.id">
          <td class="align-middle">{{ transaction.payment_reference }}</td>
          <td class="align-middle" :class="[transaction.numericValue > 0 ? 'text-success' : 'text-danger']">{{
            transaction.value }}</td>
          <td class="align-middle">{{ transaction.new_balance }}€</td>
          <td class="align-middle">{{ transaction.payment_type }}</td>
          <td class="align-middle">{{ transaction.datetime }}</td>
          <td class="align-middle">
            <span v-if="(isEditing && !isTransactionSelected(transaction)) || !isEditing">
              {{ transaction.category }}
            </span>
            <div v-else class="d-flex justify-content-center gap-1" style="min-width: 150px;">
              <select id="inputUpdateType" :disabled="isParentLoading" style="font-size: 14px;"
                v-model="categoryOfSelectedTransaction" class="form-select form-select-sm">
                <option v-for="category in selectedTypeCategories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
          </td>
          <td class="align-middle">
            <span v-if="(isEditing && !isTransactionSelected(transaction)) || !isEditing">
              {{ transaction.description }}
            </span>
            <div v-else class="d-flex justify-content-center gap-1" style="min-width: 150px;">
              <textarea class="form-control" id="inputUpdateDescription" rows="1" :disabled="isParentLoading" type="text"
                v-model="descriptionOfSelectedTransaction" maxlength="50" style="resize:none;"></textarea>
            </div>
          </td>
          <td>

            <div class="d-flex justify-content-end gap-1">
              <button v-if="(isEditing && isTransactionSelected(transaction))" type="button"
                class="btn btn-sm btn-success" @click="updateTransaction(transaction)" :disabled="isParentLoading">
                <i class="bi bi-box-arrow-down"></i>
              </button>
              <button class="btn btn-sm btn-light" @click="toggleEditingTransaction(transaction)"
                :disabled="isParentLoading || (isEditing && !isTransactionSelected(transaction))">
                <i class="bi" :class="{
                  'bi-x-circle': ((isEditing && isTransactionSelected(transaction))),
                  'bi-pencil-square': ((isEditing && !isTransactionSelected(transaction)) || !isEditing)
                }">
                </i>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.completed {
  text-decoration: line-through;
}

button {
  margin-left: 3px;
  margin-right: 3px;
}

tr.disabled {
  opacity: 50%;
}

.bi {
  margin: 0 !important;
}
</style>
    
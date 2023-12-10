<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import TransactionTable from "./TransactionTable.vue"
import { useAuthStore } from '../../stores/auth';
import { useTransactionStore } from '../../stores/transaction';
import { useCategoryStore } from '../../stores/category'
import { useToast } from 'vue-toastification';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';

const authStore = useAuthStore()
const transactionStore = useTransactionStore()
const categoryStore = useCategoryStore()
const toast = useToast()
const isLoading = ref(false)
const categories = ref([])

const typeCategories = computed(() => {
  if (transactionStore.selectedType === transactionStore.types[0].value) {
    return [{
      id: null,
      name: "Please select a Type"
    }]
  }
  const collection = [{
    id: null,
    name: "All"
  },
  {
    id: 'uncategorized',
    name: "-- No Category --"
  }]

  if (transactionStore.selectedType === "D") {
    collection.push(...categories.value.filter(category => category.type === "D"))
  }
  else if (transactionStore.selectedType === "C") {
    collection.push(...categories.value.filter(category => category.type === "C"))
  }

  return collection;
})

watch(() => transactionStore.selectedType, () => {
  transactionStore.selectedCategory = null
})

const loadTransactions = async (page = 1) => {
  try {
    isLoading.value = true
    await transactionStore.load(authStore.user.username, page)
  } catch {
    toast.error("Something went wrong. Please try again later.")
  }
  finally {
    isLoading.value = false
  }
}

const search = (value) => {
  transactionStore.searchValue = value
  loadTransactions(1)
}

const resetPaymentType = () => {
  transactionStore.selectedPaymentType = null
  loadTransactions(1)
}

const resetDate = () => {
  transactionStore.selectedDate = null
  loadTransactions(1)
}

const resetType = () => {
  transactionStore.selectedType = null
  transactionStore.selectedCategory = null
  loadTransactions(1)
}

const resetCategory = () => {
  transactionStore.selectedCategory = null
  loadTransactions(1)
}


const inputMaxDate = new Date().toISOString().split("T")[0]

onMounted(async () => {
  await loadTransactions()
  categories.value = await categoryStore.getCategories(authStore.user.username)
})
</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3>Transactions</h3>
    </div>
  </div>
  <hr>
  <router-link class="btn btn-success btn-sm" to="/transactions/create">
    <i class="bi bi-send-plus"></i>
    &nbsp;
    <span style="font-size:15px">New Debit Transaction</span>
  </router-link>
  <hr>
  <div class="mb-3 row">
    <div class="col-xs-12 col-md-3">
      <label for="inputPaymentType" style="font-size: 14px;" class="form-label">Payment Type</label>
      <div class="input-group input-group-sm">
        <select id="inputPaymentType" :disabled="isLoading" style="font-size: 14px;"
          v-model="transactionStore.selectedPaymentType" class="form-select" @change="loadTransactions()">
          <option v-for="paymentType in transactionStore.paymentTypes" :key="paymentType.value"
            :value="paymentType.value">
            {{ paymentType.text }}
          </option>
        </select>
        <button :disabled="!transactionStore.selectedPaymentType || isLoading" class="btn btn-light btn-sm"
          @click="resetPaymentType">
          <i class="bi bi-x"></i>
        </button>
      </div>
    </div>
    <div class="col-xs-12 col-md-3">
      <label for="inputType" style="font-size: 14px;" class="form-label">Type</label>
      <div class="input-group input-group-sm">
        <select id="inputType" :disabled="isLoading" style="font-size: 14px;" v-model="transactionStore.selectedType"
          class="form-select" @change="loadTransactions()">
          <option v-for="transactionType in transactionStore.types" :key="transactionType.value"
            :value="transactionType.value">
            {{ transactionType.text }}
          </option>
        </select>
        <button :disabled="transactionStore.selectedType == null || isLoading" class="btn btn-light btn-sm"
          @click="resetType">
          <i class="bi bi-x"></i>
        </button>
      </div>
    </div>
    <div class="col-xs-12 col-md-6">
      <label for="inputCategory" style="font-size: 14px;" class="form-label">Type</label>
      <div class="input-group input-group-sm">
        <select :disabled="!transactionStore.selectedType || isLoading" id="inputType" style="font-size: 14px;"
          v-model="transactionStore.selectedCategory" class="form-select" @change="loadTransactions()">
          <option v-for="category in typeCategories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <button :disabled="!transactionStore.selectedCategory || isLoading" class="btn btn-light btn-sm"
          @click="resetCategory">
          <i class="bi bi-x"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="mb-3 row">
    <div class="col-xs-12 col-md-3">
      <label for="inputDate" style="font-size: 14px;" class="form-label">Date</label>
      <div class="input-group input-group-sm">
        <input :disabled="isLoading" type="date" class="form-control" id="inputDate"
          v-model="transactionStore.selectedDate" @change="loadTransactions" :max="inputMaxDate" />
        <button :disabled="!transactionStore.selectedDate || isLoading" class="btn btn-light btn-sm" @click="resetDate">
          <i class="bi bi-x"></i>
        </button>
      </div>
    </div>
    <div class="col-xs-12 col-md-9">
      <label for="inputSearch" class="form-label">Search</label>
      <input id="inputSearch" class="form-control" v-debounce:300ms="search" type="text"
        placeholder="Search by payment reference or description" aria-label="Search" style="font-size: 14px;" />
    </div>
  </div>
  <transaction-table :transactions="transactionStore.paginatedTransactions.data"></transaction-table>
  <Bootstrap5Pagination :data="transactionStore.paginatedTransactions" @pagination-change-page="loadTransactions" />
</template>
  
<style scoped>
.bi {
  margin: 0 !important;
}
</style>
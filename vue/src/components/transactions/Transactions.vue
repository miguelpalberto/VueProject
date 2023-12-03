<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import TransactionTable from "./TransactionTable.vue"
import { useAuthStore } from '../../stores/auth';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';

const authStore = useAuthStore()
const transactions = ref([])

const props = defineProps({
  transactionsTitle: {
    type: String,
    default: 'Transactions'
  },
})

const loadTransactions = (page = 1) => {
  const vcardId = authStore.user.username    // todo Change later when authentication is implemented
  axios.get(`vcards/${vcardId}/transactions`, { params: { page: page } })
    .then((response) => {
      transactions.value = response.data
    })
    .catch((error) => {
      console.log(error)
    })
}

onMounted(() => {
  loadTransactions()
})
</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3>{{ transactionsTitle }}</h3>
    </div>
    <div class="mx-2 total-filtro">
    </div>
  </div>
  <hr>
  <router-link class="btn btn-success btn-sm" to="/transactions/create">
    <i class="bi bi-send-plus"></i> New Bank Transaction
  </router-link>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">
    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectProject" class="form-label">Filter by entity:</label>
    </div>

    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectCompleted" class="form-label">Filter by category:</label>
    </div>
  </div>
  <transaction-table :transactions="transactions.data"></transaction-table>
  <Bootstrap5Pagination :data="transactions" @pagination-change-page="loadTransactions" />
</template>


<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 0.35rem;
}

.btn-addtask {
  margin-top: 1.85rem;
}
</style>

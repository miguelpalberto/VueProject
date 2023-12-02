<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import TransactionTable from "./TransactionTable.vue"
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore()

const loadTransactions = () => {

  const vcardId = authStore.user.username    // todo Change later when authentication is implemented
  axios.get('vcards/' + vcardId + '/transactions')
    .then((response) => {
      console.log(response)//
      transactions.value = response.data.data
    })
    .catch((error) => {
      console.log(error)
    })
}

const props = defineProps({
  transactionsTitle: {
    type: String,
    default: 'Transactions'
  },
})

const transactions = ref([])

onMounted(() => {//so depois de estar tudo carregado
  loadTransactions()
})

</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3>{{ transactionsTitle }}</h3>
    </div>
    <div class="mx-2 total-filtro">
      <!-- <h5 class="mt-4">Total: {{ totalTasks }}</h5> -->
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
      <!-- <select
          class="form-select"
          id="selectProject"
          v-model="filterByProjectId"
          >
          <option value="-1">Any</option>
          <option :value="null">-- No category --</option>
          <option
          v-for="prj in projects"
            :key="prj.id"
            :value="prj.id"
          >{{prj.name}}</option>
        </select> -->
    </div>

    <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectCompleted" class="form-label">Filter by category:</label>
      <!-- <select
          class="form-select"
          id="selectCompleted"
          v-model="filterByCompleted"
          >
          <option value="-1">Any</option>
          <option value="0">Pending Tasks</option>
          <option value="1">Completed Tasks</option>
        </select> -->
    </div>

        <!-- <div class="mx-2 mt-2">
      <button
      type="button"
        class="btn btn-success px-4 btn-addtask"
        @click="addTask"
        ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Task</button>
    </div> -->
  </div>
  <transaction-table :transactions="transactions" :showId="true"></transaction-table>
  <!-- dentro do transaction-table
    :tasks="filteredTasks"
    :showId="true"
    :showOwner="false"
    @edit="editTask"
    @deleted="deletedTask" -->
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

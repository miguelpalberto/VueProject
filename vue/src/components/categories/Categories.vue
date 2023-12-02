<script setup>
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import CategoryTable from "./CategoryTable.vue"
import { useAuthStore } from '../../stores/auth';


const authStore = useAuthStore()

const loadCategories = () => {

  const vcardId = authStore.user.username    // todo Change later when authentication is implemented
  axios.get('vcards/' + vcardId + '/categories')
    .then((response) => {
      console.log(response)//
      categories.value = response.data.data
    })
    .catch((error) => {
      console.log(error)
    })
}
const debitCategories = computed(() => {
  return categories.value.filter(t => t.type == 'D')
})

const creditCategories = computed(() => {
  return categories.value.filter(t => t.type == 'C')
})

const props = defineProps({
  categoriesTitle: {
    type: String,
    default: 'Categories'
  },
})

const categories = ref([])

onMounted(() => {//so depois de estar tudo carregado
  loadCategories()
})

</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3 class="mt-4">{{ categoriesTitle }}</h3>
    </div>
    <div class="mx-2 total-filtro">
      <!-- <h5 class="mt-4">Total: {{ totalTasks }}</h5> -->
    </div>
  </div>
  <hr>
  <router-link class="btn btn-success btn-sm" to="/categories/create">
    <i class="bi bi-send-plus"></i> New Category
  </router-link>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">

    <!-- <div class="mx-2 mt-2 flex-grow-1 filter-div">
      <label for="selectCompleted" class="form-label">Filter by transaction type:</label>
      <select
          class="form-select"
          id="selectCompleted"
          v-model="filterByCompleted"
          >
          <option value="-1">Any</option>
          <option value="0">Pending Tasks</option>
          <option value="1">Completed Tasks</option>
        </select>
    </div>

        <div class="mx-2 mt-2">
      <button
      type="button"
        class="btn btn-success px-4 btn-addtask"
        @click="addTask"
        ><i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Task</button>
    </div> -->
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-6">
      <h4>Debit</h4>
      <category-table :categories="debitCategories" :showId="true"></category-table>
    </div>
    <div class="col-xs-12 col-md-6">
      <h4>Credit</h4>
      <category-table :categories="creditCategories" :showId="true"></category-table>
    </div>
  </div>
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

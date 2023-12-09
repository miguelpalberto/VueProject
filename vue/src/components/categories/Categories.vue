<script setup>
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import CategoryTable from "./CategoryTable.vue"
import { useAuthStore } from '../../stores/auth';
import { useCategoryStore } from '../../stores/category';
import { useRouter } from 'vue-router'
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

const paginatedResult = ref([])
const isLoading = ref(false)
const authStore = useAuthStore()
const categoryStore = useCategoryStore()
const router = useRouter()
const selectedType = ref(categoryStore.types[0].value)

 const loadCategories = async (page = 1, searchValue = null) => {
  isLoading.value = true

   const vcardId = authStore.user.username
    try {
      await categoryStore.loadCategories(vcardId, page, searchValue, selectedType.value)

      //console.log(paginatedResult.value)
    }
    catch (error) {
      toast.error('Error loading Categories. Please try again.')//console.log(error)
    }
    finally {
        isLoading.value = false
    }
}
 const search = (value) => {
    loadCategories(1, value)
 }

// const editCategory = (category) => {
//     router.push({ name: 'Category', params: { id: category.id } })
// }

//Chamado pelo CategoryTable, elimina no frontend
const deletedFunction = (deletedCategory) => {
    categoryStore.deleteCategory(deletedCategory)
}

const editedFunction = (editedCategory) => {
    let idx = categories.value.findIndex((t) => t.id === editedCategory.id)
    console.log(idx)
    if (idx >= 0) {
      categories.value.splice(idx, 1)
    }
}
const allTypesCategories = computed(() => {
  if(!categoryStore.categories){
    return []
  }
  return categoryStore.categories
})
// const debitCategories = computed(() => {
//   if(!categoryStore.categories){
//     return []
//   }
//   return categoryStore.categories.filter(t => t.type === 'D')
// })
// const creditCategories = computed(() => {
//   if(!categoryStore.categories){
//     return []
//   }
//   return categoryStore.categories.filter(t => t.type === 'C')
// })

const props = defineProps({
  categoriesTitle: {
    type: String,
    default: 'Categories'
  },
})

const categories = ref([]) //categorias ja estao nas store

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
    <i class="bi bi-send-plus"></i> 
    <span>New Category</span>
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
  <div class="container">
  <div class="row">
    <div class="col-xs-12 col-md-8 mx-auto">
      <!-- <h4>Debit</h4> -->
      <div class="mb-1 row">
        <div class="col-xs-12 col-md-9">
            <label for="inputSearch" class="form-label">Search</label>
            <input id="inputSearch" class="form-control" v-debounce:300ms="search" type="text"
                placeholder="Search by name" aria-label="Search" style="font-size: 14px;"/>
        </div>
        <div class="col-xs-12 col-md-3">
          <label for="inputSearch" class="form-label">Type</label>
          <select id="inputType" style="font-size: 14px;" v-model="selectedType" class="form-select" @change="loadCategories()">
              <option v-for="type in categoryStore.types" :key="type.value" :value="type.value">{{ type.text }}</option>
          </select>
      </div>
      </div>
      <!-- todo show id true so se user for admin: -->
      <category-table 
      :is-parent-loading="isLoading" 
      modalId="categoryTableModal" 
      :categories="categoryStore.paginatedCategories.data"
      :showId="false" 
      @edited="editedFunction" 
      @deleted="deletedFunction">
      </category-table>
      <Bootstrap5Pagination :data="paginatedResult" @pagination-change-page="loadCategories" />
    </div>
  </div>
    <!-- <div class="col-xs-12 col-md-6">
      <h4>Credit</h4>
      <div class="mb-1 row">
        <div class="col-xs-12 col-md-9">
            <label for="inputSearch2" class="form-label"></label>
            <input id="inputSearch2" class="form-control" v-debounce:300ms="searchc" type="text"
                placeholder="Search by name" aria-label="Search" style="font-size: 14px;"/>
        </div>
      </div>
      <category-table
      :is-parent-loading="isLoading" 
      :categoriescredit="paginatedResult.data" 
      modalId="creditTableModal" 
      :categories="creditCategories" 
      :showId="false"     
      @edited="editedFunction"
      @deleted="deletedFunction">
    </category-table>
    <Bootstrap5Pagination :data="paginatedResult" @pagination-change-page="loadCategories" />
    </div> -->
  </div>

</template>


<style scoped>
.filter-div {
  min-width: 12rem;
}

.total-filtro {
  margin-top: 0.35rem;
}


</style>

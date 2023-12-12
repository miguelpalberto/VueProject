<script setup>
import { ref, onMounted, computed } from 'vue'
import DefaultCatTable from "./DefaultCatTable.vue"
import { useDefaultCatStore } from '../../stores/defaultCategory';
import { Bootstrap5Pagination } from 'laravel-vue-pagination'

const isLoading = ref(false)
const defaultCatStore = useDefaultCatStore()

 const loadCategories = async (page = 1) => {
  isLoading.value = true
    try {
      await defaultCatStore.loadCategories(page)

    }
    catch (error) {
      toast.error('Error loading Default Categories. Please try again.')
    }
    finally {
        isLoading.value = false
    }
}
 const search = (value) => {
  defaultCatStore.searchValue = value
    loadCategories(1)
 }

const deletedFunction = (deletedCategory) => {
  defaultCatStore.deleteCategory(deletedCategory)
}

const props = defineProps({
  categoriesTitle: {
    type: String,
    default: 'Default Categories'
  },
})

const categories = ref([])

onMounted(() => {
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
  <router-link class="btn btn-success btn-sm" to="/defaultCategories/create">
    <i class="bi bi-send-plus"></i> 
    <span>New Default Category</span>
  </router-link>
  <hr>
  <div class="mb-3 d-flex justify-content-between flex-wrap">

  </div>
  <div class="container">
  <div class="row">
    <div class="col-xs-12 col-md-8 mx-auto">

      <div class="mb-1 row">
        <div class="col-xs-12 col-md-9">
            <label for="inputSearch" class="form-label">Search</label>
            <input id="inputSearch" class="form-control" v-debounce:300ms="search" type="text"
                placeholder="Search by name" aria-label="Search" style="font-size: 14px;"/>
        </div>
        <div class="col-xs-12 col-md-3">
          <label for="inputSearch" class="form-label">Type</label>
          <select id="inputType" style="font-size: 14px;" v-model="defaultCatStore.selectedType" class="form-select" @change="loadCategories()">
              <option v-for="type in defaultCatStore.types" :key="type.value" :value="type.value">{{ type.text }}</option>
          </select>
      </div>
      </div>

      <default-cat-table 
      :is-parent-loading="isLoading" 
      modalId="defaultCategoryTableModal" 
      :categories="defaultCatStore.paginatedCategories.data"
      :showId="false" 
      @edited="editedFunction" 
      @deleted="deletedFunction"/>
      <Bootstrap5Pagination :data="defaultCatStore.paginatedCategories" @pagination-change-page="loadCategories" />
    </div>
  </div>
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

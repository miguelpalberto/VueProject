<script setup>
import { ref, onMounted } from 'vue'
import DefaultCatTable from "./DefaultCatTable.vue"
import { useDefaultCatStore } from '../../stores/defaultCategory';
import { Bootstrap5Pagination } from 'laravel-vue-pagination'
import { useToast } from 'vue-toastification'

const isLoading = ref(false)
const toast = useToast()
const defaultCatStore = useDefaultCatStore()

const loadDefaultCategories = async (page = 1) => {
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
  loadDefaultCategories(1)
}

const deleteCategory = (category) => {
  try {
    isLoading.value = true
    defaultCatStore.remove(category)
    toast.success('Default Category deleted')
  }
  catch (error) {
    toast.error('Error deleting default category. Please try again.')
  }
  finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadDefaultCategories()
})

</script>

<template>
  <div class="d-flex justify-content-between">
    <div class="mx-2">
      <h3>Default Categories</h3>
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
              placeholder="Search by name" aria-label="Search" style="font-size: 14px;" />
          </div>
          <div class="col-xs-12 col-md-3">
            <label for="inputSearch" class="form-label">Type</label>
            <select id="inputType" style="font-size: 14px;" v-model="defaultCatStore.selectedType" class="form-select"
              @change="loadDefaultCategories()">
              <option v-for="iterationType in defaultCatStore.types" :key="iterationType.value"
                :value="iterationType.value">{{ iterationType.text }}</option>
            </select>
          </div>
        </div>

        <default-cat-table :is-parent-loading="isLoading"
                            modalId="defaultCategoryTableModal"
                            :categories="defaultCatStore.paginatedCategories.data"
                            @delete-category="deleteCategory" />
        <Bootstrap5Pagination :data="defaultCatStore.paginatedCategories" @pagination-change-page="loadDefaultCategories" />
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

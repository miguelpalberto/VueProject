<script setup>
import { ref, onMounted } from 'vue'
import CategoryTable from "./CategoryTable.vue"
import { useAuthStore } from '../../stores/auth';
import { useCategoryStore } from '../../stores/category';
import { useToast } from 'vue-toastification';

const isLoading = ref(false)
const authStore = useAuthStore()
const categoryStore = useCategoryStore()
const toast = useToast()

const loadCategories = async (page = 1) => {
  isLoading.value = true

  const vcardId = authStore.user.username
  try {
    await categoryStore.loadCategories(vcardId, page)
  }
  catch (error) {
    toast.error('Error loading Categories. Please try again.')
  }
  finally {
    isLoading.value = false
  }
}
const search = (value) => {
  categoryStore.searchValue = value
  loadCategories(1)
}

const deleteCategory = async (category) => {
  try {
    isLoading.value = true
    await categoryStore.remove(authStore.user.username, category).then(() => {
      toast.success('Category deleted')
    })
  }
  catch (error) {
    if (error.response.status == 422) {
            toast.error(error.response.data.error)
        } else {
            toast.error('Error deleting category. Please try again.')
        }
  }
  finally {
    isLoading.value = false
  }
}

onMounted(() => {//so depois de estar tudo carregado
  loadCategories()
})

</script>

<template>
  <h3 class="mb-3">Categories</h3>
  <hr>
  <router-link class="btn btn-success btn-sm" to="/categories/create">
    <i class="bi bi-send-plus"></i>
    <span>New Category</span>
  </router-link>
  <hr>

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
            <select id="inputType" style="font-size: 14px;" v-model="categoryStore.selectedType" class="form-select"
              @change="loadCategories()">
              <option v-for="categoryType in categoryStore.types" :key="categoryType.value" :value="categoryType.value">{{
                categoryType.text }}</option>
            </select>
          </div>
        </div>
        <category-table modalId="categoryTableModal" :isParentLoading="isLoading"
          :categories="categoryStore.paginatedCategories.data" @deleteCategory="deleteCategory" />
        <Bootstrap5Pagination :data="categoryStore.paginatedCategories" @pagination-change-page="loadCategories" :limit="1" :keepLength="true" />
      </div>
    </div>
  </div>
</template>
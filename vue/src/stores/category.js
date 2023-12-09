import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'


export const useCategoryStore = defineStore('category', () => {
    const socket = inject('socket')
    const paginatedCategories = ref([])
    const categories = computed(() => paginatedCategories.value.data ?? [])
    const toast = useToast()
    //const categories = ref([])
    const types = [
        { value: 'all', text: 'All' },
        { value: 'D', text: 'Debit only' },
        { value: 'C', text: 'Credit only' }
    ]
    const searchValue = ref(null)
    const selectedType = ref(types[0].value)

    const loadCategories = async (vcard, page = 1) => {
        try {
            // const params = {
            //     page: page
            // }
            // if (params.type && params.type !== 'all' && types.some((s) => s.value === params.type)) {
            //     params.type = selectedStatus
            // }
            //console.log("params: " + params + params.page + params.search)

            const params = {
                page: page
            }
    
            if (selectedType.value && selectedType.value !== 'all' && types.some((s) => s.value === selectedType.value)) {
                params.type = selectedType.value
            }
    
            //check if searchValue is not null and not empty
            if (searchValue.value) {
                params.name = searchValue.value
            }

            const response = await axios.get(`vcards/${vcard}/categories`, { params });
            //categories.value = response.data.data;
            paginatedCategories.value = response.data;
        } catch (error) {
            throw error;
        }
    }


    const deleteCategory = (category) => {
        let idx = categories.value.findIndex((t) => t.id === category.id)
        if (idx >= 0) {
            categories.value.splice(idx, 1)
        }
    }

    const updateCategory = async (category) => {
        try{
            await axios.put(`categories/${category.id}`, category)
        }catch(error){
            throw error
        }

    }

    return { categories, types, paginatedCategories, updateCategory, loadCategories, deleteCategory, selectedType, searchValue};
})

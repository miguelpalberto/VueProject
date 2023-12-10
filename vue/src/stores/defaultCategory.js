import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'


export const useDefaultCatStore = defineStore('category', () => {
    const socket = inject('socket')
    const paginatedCategories = ref([])
    const categories = computed(() => paginatedCategories.value.data ?? [])
    const toast = useToast()

    const types = [
        { value: 'all', text: 'All' },
        { value: 'D', text: 'Debit only' },
        { value: 'C', text: 'Credit only' }
    ]
    const searchValue = ref(null)
    const selectedType = ref(types[0].value)

    const loadCategories = async (page = 1) => {
        try {
            const params = {
                page: page
            }
    
            if (selectedType.value && selectedType.value !== 'all' && types.some((s) => s.value === selectedType.value)) {
                params.type = selectedType.value
            }

            if (searchValue.value) {
                params.name = searchValue.value
            }
            const response = await axios.get(`/defaultCategories`, { params });

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
            await axios.put(`defaultCategories/${category.id}`, category)
        }catch(error){
            throw error
        }

    }

    return { categories, types, paginatedCategories, updateCategory, loadCategories, deleteCategory, selectedType, searchValue};
})

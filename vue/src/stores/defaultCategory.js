import axios from 'axios'
import { ref, computed } from 'vue'
import { defineStore } from 'pinia'


export const useDefaultCatStore = defineStore('category', () => {
    const paginatedCategories = ref([])
    const categories = computed(() => paginatedCategories.value.data ?? [])

    const types = [
        { value: 'all', text: 'All' },
        { value: 'D', text: 'Debit only' },
        { value: 'C', text: 'Credit only' }
    ]
    const searchValue = ref(null)
    const selectedType = ref(types[0].value)

    const loadCategories = async (page = 1) => {
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
    }

    const updateCategory = async (category) => {
        await axios.put(`defaultCategories/${category.id}`, category)
    }


    const remove = async (category) => {
        await axios.delete('defaultCategories/' + category.id)
            .then(async () => {
                await loadCategories(computeQueryPage())
            })
    }


    const computeQueryPage = () => {
        if (paginatedCategories.value.current_page == 1) {
            return 1;
        }

        return paginatedCategories.value.data.length == 1 ? paginatedCategories.value.current_page - 1 : paginatedCategories.value.current_page
    }

    return { categories, types, paginatedCategories, updateCategory, loadCategories, selectedType, searchValue, remove };
})

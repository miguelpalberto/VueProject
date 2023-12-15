import axios from 'axios'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCategoryStore = defineStore('category', () => {
    const paginatedCategories = ref([])

    const types = [
        { value: 'all', text: 'All' },
        { value: 'D', text: 'Debit only' },
        { value: 'C', text: 'Credit only' }
    ]
    const searchValue = ref(null)
    const selectedType = ref(types[0].value)

    const loadCategories = async (vcard, page = 1) => {
        const params = {
            page: page
        }

        if (selectedType.value && selectedType.value !== 'all' && types.some((s) => s.value === selectedType.value)) {
            params.type = selectedType.value
        }

        if (searchValue.value) {
            params.name = searchValue.value
        }

        const response = await axios.get(`vcards/${vcard}/categories`, { params });
        paginatedCategories.value = response.data;
    }

    const getCategories = async (vcard) => {
        const response = await axios.get(`vcards/${vcard}/categories?dontPaginate=true`);
        return response.data.data;
    }

    const updateCategory = async (category) => {
        await axios.put(`categories/${category.id}`, category)
    }

    const remove = async (vcard, category) => {
        await axios.delete('categories/' + category.id)
            .then(async () => {
                await loadCategories(vcard, computeQueryPage())
            })
    }

    const computeQueryPage = () => {
        if (paginatedCategories.value.current_page == 1) {
            return 1;
        }

        return paginatedCategories.value.data.length == 1 ? paginatedCategories.value.current_page - 1 : paginatedCategories.value.current_page
    }

    return { types, paginatedCategories, updateCategory, loadCategories, remove, getCategories, selectedType, searchValue };
})

import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'


export const useCategoryStore = defineStore('category', () => {
    const categories = ref([])
    const types = [
        { value: 'all', text: 'All' },
        { value: 'D', text: 'Debit only' },
        { value: 'C', text: 'Credit only' }
    ]

    const loadCategories = async (vcard, params) => {
        try {
            // const params = {
            //     page: page
            // }
            // if (params.type && params.type !== 'all' && types.some((s) => s.value === params.type)) {
            //     params.type = selectedStatus
            // }
            //console.log("params: " + params + params.page + params.search)
            const response = await axios.get(`vcards/${vcard}/categories`, { params });
            //console.log("response.data: " + response.data) 
            //console.log("response.data.data: " + response.data.data) 
            categories.value = response.data.data;
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

    return { categories, updateCategory, loadCategories, deleteCategory, types};
})

import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'


export const useCategoryStore = defineStore('category', () => {
    const categories = ref([])


    const loadCategories = async (vcard, params) => {
        try {
            //console.log("params: " + params + params.page + params.search)
            const response = await axios.get(`vcards/${vcard}/categories`, { params });
            console.log("response.data: " + response.data) 
            console.log("response.data.data: " + response.data.data) 
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

    return { categories, updateCategory, loadCategories, deleteCategory};
})
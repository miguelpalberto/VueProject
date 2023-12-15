import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'

export const useDefaultCatStore = defineStore('defaultCategory', () => {
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

    const insertCategory = async (category) => {
        await axios.post('defaultCategories', category)
            .then(() => {
                socket.emit('defaultCategoryCreated', category)
            })
    }

    const updateCategory = async (category) => {
        await axios.put(`defaultCategories/${category.id}`, category)
            .then(() => {
                socket.emit('defaultCategoryUpdated', category)
            })
    }

    const remove = async (category) => {
        await axios.delete('defaultCategories/' + category.id)
            .then(async () => {
                socket.emit('defaultCategoryDeleted', category)
                await loadCategories(computeQueryPage())
            })
    }

    socket.on('defaultCategoryCreated', async (insertedCategory) => {
        toast.info('Default category ' + insertedCategory.name + ' has been added.')
        await loadCategories(computeQueryPage())
    })

    socket.on('defaultCategoryUpdated', async (updatedCategory) => {
        const idx = paginatedCategories.value.data.findIndex((t) => t.id === updatedCategory.id)
        if (idx >= 0) {
            toast.info('Default category ' + paginatedCategories.value.data[idx].name + ' has been updated.')
            paginatedCategories.value.data[idx].name = updatedCategory.name
            paginatedCategories.value.data[idx].type = updatedCategory.type
        }
    })

    socket.on('defaultCategoryDeleted', async (deletedCategory) => {
        const idx = paginatedCategories.value.data.findIndex((t) => t.id === deletedCategory.id)
        if (idx >= 0) {
            toast.info('Default category ' + paginatedCategories.value.data[idx].name + ' has been deleted.')
            await loadCategories(computeQueryPage())
        }
    })

    const computeQueryPage = () => {
        if (paginatedCategories.value.current_page == 1) {
            return 1;
        }

        return paginatedCategories.value.data.length == 1 ? paginatedCategories.value.current_page - 1 : paginatedCategories.value.current_page
    }

    return {
        categories, types, paginatedCategories, updateCategory,
        loadCategories, selectedType, searchValue, remove, insertCategory
    };
})

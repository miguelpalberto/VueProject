<script setup>
// import { ref, watch, watchEffect } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import { ref, watch, computed } from 'vue'
import { useCategoryStore } from '../../stores/category'

const categoryStore = useCategoryStore()
const toast = useToast()
const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    showId: {
        type: Boolean,
        default: true
    },
    showEditButton: {
        type: Boolean,
        default: true
    },
    showDeleteButton: {
        type: Boolean,
        default: true
    },
    modalId: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['edit', 'deleted'])

const editingCategories = ref(props.categories)
const categoryToDelete = ref(null)
const deleteConfirmationDialog = ref(null)

watch(
    () => props.categories,
    (newCategories) => {
        editingCategories.value = newCategories
    }
)

//Edit Button related
const isLoading = ref(false)
const errors = ref(null)
const isEditing = ref(false)
const cancelEditing = () => {
    errors.value = null
    isEditing.value = false
    //formData.value.name = authStore.user.name
}
const toggleEditing = (category) => {
    isEditing.value = !isEditing.value
    if (isEditing.value) {
        editClick(category)
    }
}
const save = () => {
    errors.value = null
    updateCategorySave.value.show()
}

const editClick = (category) => {
    //todo
    emit('edit', category)
}
const deleteClick = (category) => {
    categoryToDelete.value = category
    //console.log(categoryToDelete.value)
    deleteConfirmationDialog.value.show()
}
const deleteCategoryConfirmed = async (isConfirmed) => {
    if (isConfirmed) {
        try {
            const response = await axios.delete('categories/' + categoryToDelete.value.id) //delete na bd
            let deletedCategory = response.data.data
            toast.info(`Category ${categoryToDeleteDescription.value} was deleted`)
            emit('deleted', deletedCategory) //delete no frontend (chama Categories)
        } catch (error) {
            console.log(error)
            toast.error(
                `It was not possible to delete Category ${categoryToDeleteDescription.value}!`
            )
        }
    }
}

const updateCategorySave = async () => {
    //isLoading.value = true
    try {
        await categoryStore.updateCategory(category) //(formData.value)
        cancelEditing()
        toast.success('Category updated successfully')
        emit('editedCategory', editedCategory)
    } catch (error) {
        if (error.response.status === 422) {
            console.log(error)
            errors.value = error.response.data.errors
        }

        toast.error('Something went wrong please try again')
    } finally {
        //isLoading.value = false
    }
}

const categoryToDeleteDescription = computed(() =>
    categoryToDelete.value
        ? `\"${categoryToDelete.value.name}\" (#${categoryToDelete.value.id})`
        : ''
)
const categoryToDeleteDescriptionNoId = computed(() =>
    categoryToDelete.value ? `\"${categoryToDelete.value.name}\"` : ''
)
</script>

<template>
    <confirmation-dialog
        ref="deleteConfirmationDialog"
        confirmationBtn="Delete category"
        :modalId="modalId"
        :msg="
            showId
                ? `Do you really want to delete category ${categoryToDeleteDescription} ?`
                : `Do you really want to delete category ${categoryToDeleteDescriptionNoId}  ?`
        "
        @response="deleteCategoryConfirmed"
    >
    </confirmation-dialog>

    <table class="table">
        <thead>
            <tr>
                <th v-if="showId">#</th>
                <th>Name</th>
                <th v-if="showCompletedButton || showEditButton || showDeleteButton"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="category in props.categories" :key="category.id">
                <td v-if="showId">{{ category.id }}</td>
                <td>{{ category.name }}</td>
                <td
                    class="text-end"
                    v-if="showCompletedButton || showEditButton || showDeleteButton"
                >
                    <div class="d-flex justify-content-end">



                        <div v-if="showEditButton">
                            <button
                                class="btn btn-light"
                                v-if="!isEditing"
                                @click="toggleEditing(category)"
                            >
                                <i class="bi bi-xs bi-pencil"></i>
                            </button>
                        </div>
                        <button
                            class="btn btn-outline-success"
                            v-if="isEditing"
                            :disabled="isLoading"
                            @click="save"
                        >
                            <span
                                class="spinner-border spinner-border-sm mx-1"
                                aria-hidden="true"
                                v-if="isLoading"
                            ></span>
                            <span role="Save">Save</span>
                        </button>
                        <button
                            class="btn btn-outline-secondary"
                            v-if="isEditing"
                            :disabled="isLoading"
                            @click="cancelEditing"
                        >
                            Cancel
                        </button>
                        <button
                            class="btn btn-xs btn-light"
                            @click="deleteClick(category)"
                            v-if="showDeleteButton"
                        >
                            <i class="bi bi-xs bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<style scoped>
.completed {
    text-decoration: line-through;
}

button {
    margin-left: 3px;
    margin-right: 3px;
}
.tables-container {
    display: flex;
    justify-content: space-between;
}

.table-wrapper {
    flex: 1;
    margin: 0 10px; /* Adjust the margin as needed */
}

.table {
    width: 100%;
    border-collapse: collapse;
}
</style>

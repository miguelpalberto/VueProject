<script setup>
import axios from 'axios'
import { useToast } from 'vue-toastification'
import { ref, watch, computed } from 'vue'
import { useDefaultCatStore } from '../../stores/defaultCategory'

const categoryStore = useDefaultCatStore()
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

const emit = defineEmits(['edit', 'deleted', 'save'])

const editingCategories = ref(props.categories)
const selectedCategory = ref(null)
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
const nameOfSelectedCategory = ref(null)


const isCategorySelected = (category) => {
    if (!selectedCategory.value) {
        return false
    }
    return selectedCategory.value.id == category.id
}


const stopEditing = () => {
    isEditing.value = false
    selectedCategory.value = null
    nameOfSelectedCategory.value = null
}

const startEditing = (category) => {
    isEditing.value = true
    selectedCategory.value = category
    nameOfSelectedCategory.value = category.name
}

const toggleEditing = (category) => {
    if (isEditing.value) {
        stopEditing()
    } else {
        startEditing(category)
    }
}

const save = () => {
    errors.value = null
    updateCategorySave.value.show()
}

const editClick = (category) => {
    emit('edit', category)
}
const deleteClick = (category) => {
    selectedCategory.value = category
    deleteConfirmationDialog.value.show()
}
const boolDeleteCategoryConfirmed = async (isConfirmed) => {
    isLoading.value = true
    if (isConfirmed) {
        try {
            await axios.delete('defaultCategories/' + selectedCategory.value.id) 
            toast.info(`Default Category ${categoryToDeleteDescriptionNoId.value} was deleted`)
            emit('deleted', selectedCategory.value)
        } catch (error) {
            toast.error(
                `It was not possible to delete Default Category ${categoryToDeleteDescriptionNoId.value}!`
            )
        }
        finally{
            selectedCategory.value = null
            isLoading.value = false
        }
    }
}

const updateCategorySave = async () => {
    try {
        isLoading.value = true
        selectedCategory.value.name = nameOfSelectedCategory.value
        await categoryStore.updateCategory(selectedCategory.value)
        stopEditing()
        toast.success('Default Category updated successfully')
        emit('edited', selectedCategory.value)
    } catch (error) {
        if (error.response.status == 422) {
            toast.error(error.response.data.errors.name[0])
        } else {
            toast.error('Error updating Default Category. Please try again.')
        }
    }
    finally {
        isLoading.value = false
    }
}

const categoryToDeleteDescription = computed(() =>
    selectedCategory.value
        ? `\"${selectedCategory.value.name}\" (#${selectedCategory.value.id})`
        : ''
)
const categoryToDeleteDescriptionNoId = computed(() =>
    selectedCategory.value ? `\"${selectedCategory.value.name}\"` : ''
)
</script>

<template>
    <confirmation-dialog
        ref="deleteConfirmationDialog"
        confirmationBtn="Delete Default Category"
        :modalId="modalId"
        :msg="
            `Do you really want to delete Default Category ${categoryToDeleteDescriptionNoId}?`
        "
        @response="boolDeleteCategoryConfirmed"
    >
    </confirmation-dialog>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="align-middle" v-if="showId">#</th>
                    <th class="align-middle">Name</th>
                    <th class="align-middle">Type</th>
                    <th class="align-middle" v-if="showCompletedButton || showEditButton || showDeleteButton"></th>
                </tr>
            </thead>
        <tbody>
            <tr :class="{ 'deactive': isEditing && !isCategorySelected(category) }" v-for="category in props.categories" :key="category.id">
                <td v-if="showId">{{ category.id }}</td>
                <td class="align-middle">
                    <span class="text-bg" v-if="(isEditing && !isCategorySelected(category)) || !isEditing">
                        {{ category.name }}
                    </span>
                    <div v-else class="d-flex justify-content-center gap-1" style="min-width: 150px;">
                        <input id="inputName" :disabled="isParentLoading" type="text" class="form-control"
                        v-model="nameOfSelectedCategory" />
                        <button type="button" class="btn btn-success" @click="updateCategorySave(category)"
                        :disabled="isParentLoading">
                        <i class="bi bi-box-arrow-down"></i>
                    </button>
                </div>              
            </td>
            <td class="align-middle" :style="{ color: category.type === 'D' ? 'darkred' : (category.type === 'C' ? 'darkgreen' : 'black') }">
                {{ category.type === 'D' ? 'Debit' : (category.type === 'C' ? 'Credit' : 'Unknown') }}
            </td>
            <td
            class="text-end"
            v-if="showCompletedButton || showEditButton || showDeleteButton"
            >
                <div class="d-flex justify-content-end gap-1">
                    <button
                    class="btn btn-light"
                    :disabled="isLoading || (isEditing && !isCategorySelected(category))"
                    @click="toggleEditing(category)">
                        <i class="bi" :class="{
                            'bi-x-circle': ((isEditing && isCategorySelected(category))),
                            'bi-pencil-square': ((isEditing && !isCategorySelected(category)) || !isEditing)
                        }"></i>
                    </button>
                    <button
                        class="btn btn-xs btn-light"
                        @click="deleteClick(category)"
                        :disabled="isEditing"
                        >
                        <i class="bi bi-xs bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
</template>

<style scoped>
.completed {
    text-decoration: line-through;
}
.tables-container {
    display: flex;
    justify-content: space-between;
}

.table-wrapper {
    flex: 1;
    margin: 0 10px; /* Adjust the margin as needed */
}

.bi {
    margin: 0 !important;
}

.table {
    width: 100%;
    border-collapse: collapse;
}
tr.deactive {
    opacity: 50%;
}

span.text-bg{
    font-size: 15px;
}
</style>

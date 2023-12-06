<script setup>
// import axios from 'axios'
// import { ref, watch, watchEffect } from 'vue'
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch, computed } from "vue"


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
// const emit = defineEmits([
//     // "completeToggled",
//     // "edit",
//     // "deleted",
// ])

// watch(
//   () => props.tasks,
//   (newTasks) => {
//     //editingTasks.value = newTasks;
//   }
// );

const editClick = (category) => {
    emit('edit', category)
}

const deleteClick = (category) => {
    categoryToDelete.value = category
    deleteConfirmationDialog.value.show()
}

const deleteCategoryConfirmed = async () => {
    try {
        const response = await axios.delete('categories/' + categoryToDelete.value.id)
        let deletedCategory = response.data.data
        toast.info(`Category ${categoryToDeleteDescription.value} was deleted`)
        emit('deleted', deletedCategory)
    } catch (error) {
        console.log(error)
        toast.error(`It was not possible to delete Category ${categoryToDeleteDescription.value}!`)
    }
}

const categoryToDeleteDescription = computed(() =>
    categoryToDelete.value
        ? `#${categoryToDelete.value.id} (${categoryToDelete.value.description})`
        : ''
)
</script>

<template>
    <confirmation-dialog
        ref="deleteConfirmationDialog"
        confirmationBtn="Delete category"
        :msg="`Do you really want to delete the category ${categoryToDeleteDescription}?`"
        @confirmed="deleteCategoryConfirmed"
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
                        <button
                            class="btn btn-xs btn-light"
                            @click="editClick(category)"
                            v-if="showEditButton"
                        >
                            <i class="bi bi-xs bi-pencil"></i>
                        </button>

                        <button
                            class="btn btn-xs btn-light"
                            @click="deleteClick(category)"
                            v-if="showDeleteButton"
                        >
                            <i class="bi bi-xs bi-x-square-fill"></i>
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

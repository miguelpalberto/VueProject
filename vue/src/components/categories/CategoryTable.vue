<script setup>
// import axios from 'axios'
// import { ref, watch, watchEffect } from 'vue'

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    showId: {
        type: Boolean,
        default: true
    },
})

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
</script>

<template>
    <div class="tables-container">
        <!-- First Table -->
        <div class="table-wrapper">

            <table class="table">
                <thead>
                    <tr>
                        <th v-if="showId">#</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in props.categories" :key="category.id">
                        <td v-if="showId">{{ category.id }}</td>
                        <td>{{ category.name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Second Table
        <div class="table-wrapper">
            <h4>Credit</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th v-if="showId">#</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories" :key="category.id">
                        <td v-if="showId">{{ category.id }}</td>
                        <td>{{ category.name }}</td>
                    </tr>
                </tbody>
            </table>
        </div> -->
    </div>
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

<script setup>
  import axios from 'axios'
  import { ref, watch, computed, onMounted } from 'vue'

  import TaskDetail from "./TaskDetail.vue"

  const newTask = () => {
      return {
        id: null,
        owner_id: 1,  // Change it later
        project_id: null,
        completed: false,
        description: '',
        notes: '',
        total_hours: null
      }
    }
  const loadTask = (id) => {
      if (!id || (id < 0)) {
        task.value = newTask()
      } else {
        axios.get('tasks/' + id)
          .then((response) => {
            task.value = response.data.data
          })
          .catch((error) => {
            console.log(error)
          })
      }
    }
  const save = () => {
      if (operation.value == 'insert') {
        axios.post('tasks', task.value)
          .then((response) => {
            console.log('Task Created')
            console.dir(response.data.data)
          })
          .catch((error) => {
            console.dir(error)
          })
      } else {
        axios.put('tasks/' + props.id, task.value)
          .then((response) => {
            console.log('Task Updated')
            console.dir(response.data.data)
          })
          .catch((error) => {
            console.dir(error)
          })
      }
    }

  const cancel = () => {
      // Replace this code to navigate back
      loadTask(props.id)
    }

  const props = defineProps({
    id: {
      type: Number,
      default: null
    },
    fixedProject: {
      type: Number,
      default: null
    }
  })

  const task = ref(newTask())
  const projects = ref([])

  const operation = computed( () => (!props.id || props.id < 0) ? 'insert' : 'update')
  
    // beforeRouteUpdate was not fired correctly
    // Used this watcher instead to update the ID
  watch(
    () => props.id,
    (newValue) => {
        loadTask(newValue)
      }, 
    { immediate: true}
  )

  onMounted (() => {
    projects.value = []
    axios.get('projects')
      .then((response) => {
        projects.value = response.data.data
      })
      .catch((error) => {
        console.log(error)
      })
  })
</script>


<template>
  <task-detail
    :operationType="operation"
    :task="task"
    :projects="projects"
    :fixedProject="fixedProject"
    @save="save"
    @cancel="cancel"
  ></task-detail>
</template>
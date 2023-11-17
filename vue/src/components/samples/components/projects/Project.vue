<script setup>
  import axios from 'axios'
  import { ref, watch, computed, onMounted} from 'vue'
  import ProjectDetail from "./ProjectDetail.vue"

  const newProject = () => { 
    return {
      id: null,
      name: '',
      responsible_id: 1,  // Change it later
      status: 'P',
      preview_start_date: null,
      preview_end_date: null,
      real_start_date: null,
      real_end_date: null,
      total_hours: null,
      billed: false,
      total_price: null,
    }
  }

  const loadProject = (id) => {
      if (!id || (id < 0)) {
        project.value = newProject()
      } else {
        axios.get('projects/' + id)
          .then((response) => {
            project.value = response.data.data
          })
          .catch((error) => {
            console.log(error)
          })
      }
    }

  const save = () => {
      if (operation.value == 'insert') {
        axios.post('projects', project.value)
          .then((response) => {
            console.log('Project Created')
            console.dir(response.data.data)
          })
          .catch((error) => {
            console.dir(error)
          })
      } else {
        axios.put('projects/' + props.id, project.value)
          .then((response) => {
            console.log('Project Updated')
            console.dir(response.data.data)
          })
          .catch((error) => {
            console.dir(error)
          })
      }
    }


  const cancel = () => {
      // Replace this code to navigate back
      loadProject(props.id)
  }

  const props = defineProps({
      id: {
        type: Number,
        default: null
      }
    })

  const project = ref(newProject())  
  const users = ref([])  

  const operation = computed(() => {
    return (!props.id || props.id < 0) ? 'insert' : 'update'
  })

  watch(
    () => props.id, 
    (newValue) => {
          loadProject(newValue)
    }, {
      immediate: true,
    }
  )

  onMounted (() => {
    users.value = []
    axios.get('users')
      .then((response) => {
        users.value = response.data.data
      })
      .catch((error) => {
        console.log(error)
      })
  })
</script>


<template>
  <ProjectDetail
    :operationType="operation"
    :project="project"
    :users="users"
    @save="save"
    @cancel="cancel"
  ></ProjectDetail>
</template>

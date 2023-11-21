<script setup>
    import axios from 'axios'
    import { ref, watch, watchEffect } from "vue";
    
    const props = defineProps({
      transactions: {
        type: Array,
        default: () => [],
    },
      showId: {
        type: Boolean,
        default: true,
    },
  })
    
    const emit = defineEmits([
      // "completeToggled",
      // "edit",
      // "deleted",
    ]);


    watch(
      () => props.tasks,
      (newTasks) => {
        //editingTasks.value = newTasks;
      }
    );   
    // Alternative to previous watch
    // watchEffect(() => {
    //   editingTasks.value = props.tasks
    // })
    
    // const toogleClick = (task) => {
    //   axios
    //     .patch("tasks/" + task.id + "/completed", { completed: !task.completed })
    //     .then((response) => {
    //       task.completed = response.data.data.completed;
    //       emit("completeToggled", task);
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //     });
    // };
    // const editClick = (task) => {
    //   emit("edit", task);
    // };
    // const deleteClick = (task) => {
    //   axios
    //     .delete("tasks/" + task.id)
    //     .then((response) => {
    //       let deletedTask = response.data.data;
    //       emit("deleted", deletedTask);
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //     });
    // };
    </script>
    
    <!-- <template>
      <table class="table">
        <thead>
          <tr>
            <th v-if="showId">#</th>
            <th class="text-center" v-if="showCompleted">Completed</th>
            <th>Description</th>
            <th v-if="showOwner">Owner</th>
            <th v-if="showProject">Project</th>
            <th v-if="showCompletedButton || showEditButton || showDeleteButton"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in editingTasks" :key="task.id">
            <td v-if="showId">{{ task.id }}</td>
            <td class="text-center" v-if="showCompleted">
              {{ task.completed ? "yes" : "-" }}
            </td>
            <td>
              <span :class="{ completed: task.completed }">{{ task.description }}</span>
            </td>
            <td v-if="showOwner">{{ task.owner_name }}</td>
            <td v-if="showProject">{{ task.project_name }}</td>
            <td
              class="text-end"
              v-if="showCompletedButton || showEditButton || showDeleteButton"
            >
              <div class="d-flex justify-content-end">
                <button
                  class="btn btn-xs btn-light"
                  @click="toogleClick(task)"
                  v-if="showCompletedButton"
                >
                  <i
                    class="bi bi-xs"
                    :class="{
                      'bi-square': !task.completed,
                      'bi-check2-square': task.completed,
                    }"
                  ></i>
                </button>
    
                <button
                  class="btn btn-xs btn-light"
                  @click="editClick(task)"
                  v-if="showEditButton"
                >
                  <i class="bi bi-xs bi-pencil"></i>
                </button>
    
                <button
                  class="btn btn-xs btn-light"
                  @click="deleteClick(task)"
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
     -->
     <template>
      <table class="table">
        <thead>
          <tr>
            <th v-if="showId">#</th>
            <th>From/To</th><!-- payment_reference column -->
            <th>Value</th>
            <th>New Balance</th>
            <th>Category</th>
            <th>Type</th>
            <th>Date</th>
            <th>Method</th>
            <th>Description</th>
            <!-- <th>Time</th> -->
            <!-- <th v-if="showEditButton || showDeleteButton"></th> -->
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="transaction in transactions"
            :key="transaction.id"
          >
            <td v-if="showId">{{ transaction.id }}</td>
            <td>{{ transaction.payment_reference }}</td>
            <td>{{ transaction.value }}</td>
            <td>{{ transaction.new_balance }}</td>
            <td>{{ transaction.category_id }}</td>
            <td>{{ transaction.type }}</td>
            <td>{{ transaction.datetime }}</td>
            <td>{{ transaction.payment_type }}</td>
            <td>{{ transaction.description }}</td>
            <!-- <td
              class="text-end"
              v-if="showEditButton || showDeleteButton"
            >
              <div class="d-flex justify-content-end">
                <button
                  class="btn btn-xs btn-light"
                  @click="editClick(project)"
                  v-if="showEditButton"
                ><i class="bi bi-xs bi-pencil"></i>
                </button>
                </button>
              </div>
            </td> -->
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
    </style>
    
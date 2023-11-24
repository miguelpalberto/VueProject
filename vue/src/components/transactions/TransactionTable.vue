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

    </script>
  
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
    
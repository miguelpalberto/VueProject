<script setup>
const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
})
</script>
<template>
  <div class="table-responsive">

    <table class="table">
      <thead>
        <tr>
          <th>Payment Reference</th>
          <th>Value</th>
          <th>New Balance</th>
          <th>Category</th>
          <th>Type</th>
          <th>Date</th>
          <th>Method</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="transaction in transactions" :key="transaction.id">
          <td>{{ transaction.payment_reference }}</td>
          <td>{{ transaction.value }}</td>
          <td>{{ transaction.new_balance }}</td>
          <td>{{ transaction.category_id }}</td>
          <td>{{ transaction.type }}</td>
          <td>{{ transaction.datetime }}</td>
          <td>{{ transaction.payment_type }}</td>
          <td>{{ transaction.description }}</td>
        </tr>
      </tbody>
    </table>
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
</style>
    
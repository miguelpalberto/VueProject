<script setup>
import BalanceLineChart from '../components/vcards/statistics/BalanceLineChart.vue'
import BalanceLineChartAdmin from '../components/admins/statistics/BalanceLineChartAdmin.vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<template>
  <div class="container">
    <div style="max-height: 400px;" v-if="!authStore.isAdmin">
      <h3>Your Statistics</h3>
      <BalanceLineChart />
    </div>
    <div style="max-height: 400px;" v-if="authStore.isAdmin">
      <h3>Global Statistics</h3>
      <BalanceLineChartAdmin />
    </div>
  </div>
</template>
  
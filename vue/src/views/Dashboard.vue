<script setup>
import VCardStats from '../components/vcards/statistics/VCardStats.vue'
import AdminStats from '../components/admins/statistics/AdminStats.vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<template>
  <div class="container">
    <div style="max-height: 400px;" v-if="!authStore.isAdmin">
      <h3>Your Statistics</h3>
      <VCardStats />
    </div>
    <div style="max-height: 400px;" v-if="authStore.isAdmin">
      <h3>Global Statistics</h3>
      <AdminStats />
    </div>
  </div>
</template>
  
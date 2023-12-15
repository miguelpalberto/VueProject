<script setup>
</script>

<template>
  <main class="d-flex flex-column align-items-center justify-content-center">
    <div class="mb-4">
      <img src="@/assets/logogta1.svg" alt="" width="350" class="d-inline-block align-text-top" />
    </div>
    <br>
    <router-link to="/login" class="btn btn-success px-5 mb-3">
      <span role="login">Login</span>
    </router-link>
    <router-link to="/register" class="btn btn-success px-5">
      <span role="login">Register</span>
    </router-link>
  </main>
</template>

<style scoped>
  main {
    height: 70vh;
  }
</style>


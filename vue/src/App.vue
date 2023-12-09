<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

const logout = async () => {
  authStore.logout()
  clickMenuOption()
}

const clickMenuOption = () => {
  const domReference = document.getElementById('buttonSidebarExpandId')
  if (domReference) {
    if (window.getComputedStyle(domReference).display !== 'none') {
      domReference.click()
    }
  }
}
</script>

<template>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top flex-md-nowrap p-0 shadow">
    <div class="container-fluid">
      <router-link class="navbar-brand col-md-3 col-lg-2 me-0 px-3" :to="{ name: 'home' }" @click="clickMenuOption">
        <img src="@/assets/logogta1.svg" alt="" width="30" height="30" class="d-inline-block align-text-top" />
        vCard
      </router-link>
      <button id="buttonSidebarExpandId" class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item" v-if="!authStore.isAuthenticated">
            <router-link @click="clickMenuOption" class="nav-link" :class="{ active: $route.name === 'register' }"
              :to="{ name: 'register' }">
              <i class="bi bi-person-check-fill"></i>
              Register
            </router-link>
          </li>
          <li class="nav-item" v-if="!authStore.isAuthenticated">
            <router-link @click="clickMenuOption" class="nav-link" :class="{ active: $route.name === 'login' }"
              :to="{ name: 'login' }">
              <i class="bi bi-box-arrow-in-right"></i>
              Login
            </router-link>
          </li>
          <li class="nav-item dropdown" v-if="authStore.isAuthenticated">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img :src="authStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img" alt="avatarimage">
              <span class="avatar-text">{{ authStore.userName }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li>
                <router-link @click="clickMenuOption" class="dropdown-item" :class="{ active: $route.name == 'profile' }"
                  :to="{ name: 'profile' }">
                  <i class="bi bi-person-square"></i>
                  Profile
                </router-link>
              </li>
              <li v-if="authStore.isAuthenticated">
                <router-link @click="clickMenuOption" class="dropdown-item"
                  :class="{ active: $route.name === 'changePassword' }" :to="{ name: 'changePassword' }">
                  <i class="bi bi-key-fill"></i>
                  Change password
                </router-link>
              </li>
              <li>
                <router-link @click="clickMenuOption" v-if="authStore.isAuthenticated && !authStore.isAdmin"
                  class="dropdown-item" :class="{ active: $route.name === 'changeConfirmationCode' }"
                  :to="{ name: 'changeConfirmationCode' }">
                  <i class="bi bi-person-vcard"></i>
                  Change Confirmation Code
                </router-link>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <button class="dropdown-item" @click="logout"><i class="bi bi-arrow-right"></i>Logout</button>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="bg-light sidebar collapse"
        :class="authStore.isAuthenticated ? 'd-md-block col-md-3 col-lg-2' : 'd-md-none'">
        <div class="position-sticky pt-3">
          <ul v-if="authStore.isAuthenticated" class="nav flex-column">

            <li class="nav-item d-flex justify-content-between align-items-center pe-3" v-if="!authStore.isAdmin">
              <router-link @click="clickMenuOption" class="nav-link w-100 text-nowrap me-3"
                :class="{ active: $route.name === 'transactions' }" :to="{ name: 'transactions' }">
                <i class="bi bi-list-check"></i>
                Transactions
              </router-link>
              <router-link class="link-secondary" :class="{ active: $route.name === 'createTransaction' }"
                :to="{ name: 'createTransaction' }" @click="clickMenuOption">
                <i class="bi bi-xs bi-plus-circle"></i>
              </router-link>
            </li>
            <li class="nav-item" v-if="!authStore.isAdmin">
              <router-link class="nav-link w-100 me-3" @click="clickMenuOption"
                :class="{ active: $route.name === 'categories' }" :to="{ name: 'categories' }">
                <i class="bi bi-list-check"></i>
                Categories
              </router-link>
            </li>
            <li class="nav-item" v-if="authStore.isAdmin">
              <router-link class="nav-link w-100 me-3" @click="clickMenuOption"
                :class="{ active: $route.name === 'admins' }" :to="{ name: 'admins' }">
                <i class="bi bi-list-check"></i>
                Administrators
              </router-link>
            </li>
            <li class="nav-item" v-if="authStore.isAdmin">
              <router-link class="nav-link w-100 me-3" @click="clickMenuOption"
                :class="{ active: $route.name === 'vcards' }" :to="{ name: 'vcards' }">
                <i class="bi bi-list-check"></i>
                VCards
              </router-link>
            </li>
          </ul>

          <div class="d-block d-md-none">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>User</span>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item" v-if="!authStore.isAuthenticated">
                <router-link @click="clickMenuOption" class="nav-link" :class="{ active: $route.name === 'register' }"
                  :to="{ name: 'register' }">
                  <i class="bi bi-person-check-fill"></i>
                  Sign Up
                </router-link>
              </li>
              <li class="nav-item" v-if="!authStore.isAuthenticated">
                <router-link @click="clickMenuOption" class="nav-link" :class="{ active: $route.name === 'login' }"
                  :to="{ name: 'login' }">
                  <i class="bi bi-box-arrow-in-right"></i>
                  Login
                </router-link>
              </li>
              <li class="nav-item dropdown" v-if="authStore.isAuthenticated">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink2" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <img :src="authStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img" alt="avatarimage">
                  <span class="avatar-text">{{ authStore.userName }}</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink2">
                  <li>
                    <router-link @click="clickMenuOption" class="dropdown-item"
                      :class="{ active: $route.name == 'profile' }" :to="{ name: 'profile' }">
                      <i class="bi bi-person-square"></i>
                      Profile
                    </router-link>
                  </li>
                  <li>
                    <router-link @click="clickMenuOption" v-if="authStore.isAuthenticated" class="dropdown-item"
                      :class="{ active: $route.name === 'changePassword' }" :to="{ name: 'changePassword' }">
                      <i class="bi bi-key-fill"></i>
                      Change password
                    </router-link>
                  </li>
                  <li>
                    <router-link @click="clickMenuOption" v-if="authStore.isAuthenticated && !authStore.isAdmin"
                      class="dropdown-item" :class="{ active: $route.name === 'changeConfirmationCode' }"
                      :to="{ name: 'changeConfirmationCode' }">
                      <i class="bi bi-person-vcard"></i>
                      Change Confirmation Code
                    </router-link>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li v-if="authStore.isAuthenticated">
                    <button class="dropdown-item" @click="logout"><i class="bi bi-arrow-right"></i>Logout</button>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <main class="ms-sm-auto px-md-4" :class="authStore.isAuthenticated ? 'col-md-9 col-lg-10' : 'col-md-12 col-lg-12'">
        <div class="d-flex justify-content-center">
          <div class="alert alert-light" role="alert">
            <h4 v-if="authStore.isAuthenticated && !authStore.isAdmin">
              Current Balance: {{ authStore.user.balance }}€
            </h4>
          </div>
        </div>
        <div class="container-fluid py-2">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<style>
@import './assets/dashboard.css';

.avatar-img {
  margin: -1.2rem 0.8rem -2rem 0.8rem;
  width: 3.3rem;
  height: 3.3rem;
}

.avatar-text {
  line-height: 2.2rem;
  margin: 1rem 0.5rem -2rem 0;
  padding-top: 1rem;
}

.dropdown-item {
  font-size: 0.875rem;
}

.btn:focus {
  outline: none;
  box-shadow: none;
}

#sidebarMenu {
  overflow-y: auto;
}
</style>

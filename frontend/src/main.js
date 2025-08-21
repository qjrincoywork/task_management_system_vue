import "./assets/main.css"

import { createApp } from "vue"
import { createPinia } from "pinia"
import { createRouter, createWebHistory } from "vue-router"
import App from "./App.vue"

// Import stores
import { useAuthStore } from "./stores/auth"
import { useTaskStore } from "./stores/tasks"

// Import pages
import Login from "./views/Login.vue"
import Register from "./views/Register.vue"
import Dashboard from "./views/Dashboard.vue"
import AdminDashboard from "./views/AdminDashboard.vue"

// Create router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", redirect: "/dashboard" },
    { path: "/login", component: Login, meta: { guest: true } },
    { path: "/register", component: Register, meta: { guest: true } },
    { path: "/dashboard", component: Dashboard, meta: { requiresAuth: true } },
    { path: "/admin", component: AdminDashboard, meta: { requiresAuth: true, requiresAdmin: true } },
  ],
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login")
  } else if (to.meta.requiresAdmin && !authStore.user?.is_admin) {
    next("/dashboard")
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next("/dashboard")
  } else {
    next()
  }
})

// Create app
const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

app.mount("#app")
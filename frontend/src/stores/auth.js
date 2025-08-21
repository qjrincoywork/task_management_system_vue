import { defineStore } from "pinia"
import { ref, computed } from "vue"
import axios from "axios"

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null)
  const token = ref(localStorage.getItem("token"))

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.is_admin || false)

  if (token.value) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`
  }

  const login = async (credentials) => {
    try {
      const response = await axios.post("/api/login", credentials)
      const { user: userData, token: tokenData } = response.data
      
      user.value = userData
      token.value = tokenData
      
      localStorage.setItem("token", tokenData)
      axios.defaults.headers.common["Authorization"] = `Bearer ${tokenData}`

      return { success: true }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Login failed" 
      }
    }
  }

  const register = async (userData) => {
    try {
      const response = await axios.post("/api/register", userData)
      const { user: newUser, token: tokenData } = response.data
      
      user.value = newUser
      token.value = tokenData

      localStorage.setItem("token", tokenData)
      axios.defaults.headers.common["Authorization"] = `Bearer ${tokenData}`

      return { success: true }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Registration failed" 
      }
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem("token")
    delete axios.defaults.headers.common["Authorization"]
  }

  const fetchUser = async () => {
    try {
      const response = await axios.get("/api/user")
      user.value = response.data.data
      return { success: true }
    } catch (error) {
      logout()
      return { success: false }
    }
  }

  const initialize = async () => {
    if (token.value) {
      await fetchUser()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
    fetchUser,
    initialize,
  }
})
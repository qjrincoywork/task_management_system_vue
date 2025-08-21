import { defineStore } from "pinia"
import { ref, computed } from "vue"
import axios from "axios"

export const useTaskStore = defineStore("tasks", () => {
  const tasks = ref([])
  const loading = ref(false)
  const filters = ref({
    status: "all",
    priority: "all",
    search: ""
  })

  // Computed properties
  const filteredTasks = computed(() => {
    let filtered = tasks.value

    if (filters.value.status !== "all") {
      filtered = filtered.filter(task => task.status === filters.value.status)
    }

    if (filters.value.priority !== "all") {
      filtered = filtered.filter(task => task.priority === filters.value.priority)
    }

    if (filters.value.search) {
      const search = filters.value.search.toLowerCase()
      filtered = filtered.filter(task => 
        task.title.toLowerCase().includes(search) ||
        task.description.toLowerCase().includes(search)
      )
    }

    return filtered.sort((a, b) => a.order - b.order)
  })

  const taskStats = computed(() => {
    const total = tasks.value.length
    const completed = tasks.value.filter(task => task.status === "completed").length
    const pending = total - completed

    return { total, completed, pending }
  })

  // Actions
  const fetchTasks = async () => {
    loading.value = true
    try {
      const params = new URLSearchParams()
      if (filters.value.status !== "all") params.append("status", filters.value.status)
      if (filters.value.priority !== "all") params.append("priority", filters.value.priority)
      if (filters.value.search) params.append("search", filters.value.search)

      const response = await axios.get(`/api/tasks?${params}`)
      tasks.value = response.data.data
    } catch (error) {
      console.error("Failed to fetch tasks:", error)
    } finally {
      loading.value = false
    }
  }

  const createTask = async (taskData) => {
    try {
      const response = await axios.post("/api/tasks", taskData)
      tasks.value.push(response.data.task)
      return { success: true, task: response.data.task }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Failed to create task" 
      }
    }
  }

  const updateTask = async (taskId, updates) => {
    try {
      const response = await axios.put(`/api/tasks/${taskId}`, updates)
      const index = tasks.value.findIndex(task => task.id === taskId)
      if (index !== -1) {
        tasks.value[index] = response.data.task
      }
      return { success: true, task: response.data.task }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Failed to update task" 
      }
    }
  }

  const deleteTask = async (taskId) => {
    try {
      await axios.delete(`/api/tasks/${taskId}`)
      const index = tasks.value.findIndex(task => task.id === taskId)
      if (index !== -1) {
        tasks.value.splice(index, 1)
      }
      return { success: true }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Failed to delete task" 
      }
    }
  }

  const toggleTaskStatus = async (taskId) => {
    try {
      const response = await axios.post(`/api/tasks/${taskId}/toggle-status`)
      const index = tasks.value.findIndex(task => task.id === taskId)
      if (index !== -1) {
        tasks.value[index] = response.data.task
      }
      return { success: true, task: response.data.task }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Failed to toggle task status" 
      }
    }
  }

  const reorderTasks = async (reorderedTasks) => {
    try {
      const taskUpdates = reorderedTasks.map((task, index) => ({
        id: task.id,
        order: index
      }))

      await axios.post("/api/tasks/reorder", { tasks: taskUpdates })
      
      // Update local order
      reorderedTasks.forEach((task, index) => {
        const taskIndex = tasks.value.findIndex(t => t.id === task.id)
        if (taskIndex !== -1) {
          tasks.value[taskIndex].order = index
        }
      })

      return { success: true }
    } catch (error) {
      return { 
        success: false, 
        error: error.response?.data?.message || "Failed to reorder tasks" 
      }
    }
  }

  const updateFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
    fetchTasks()
  }

  return {
    tasks,
    loading,
    filters,
    filteredTasks,
    taskStats,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    toggleTaskStatus,
    reorderTasks,
    updateFilters,
  }
})
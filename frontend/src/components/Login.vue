<!-- filepath: frontend/src/components/Login.vue -->
<template>
  <form @submit.prevent="login">
    <h2>Login</h2>
    <input v-model="email" type="email" placeholder="Email" required />
    <input v-model="password" type="password" placeholder="Password" required />
    <button type="submit">Login</button>
    <p v-if="error">{{ error }}</p>
  </form>
</template>

<script setup>
import { ref } from 'vue'
const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
  error.value = ''
  try {
    const res = await fetch('http://localhost:8080/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, password: password.value })
    })
    const data = await res.json()
    if (res.ok) {
      // Save token, redirect, etc.
      localStorage.setItem('token', data.token)
      window.location.href = '/tasks'
    } else {
      error.value = data.message || 'Login failed'
    }
  } catch (e) {
    error.value = 'Network error'
  }
}
</script>
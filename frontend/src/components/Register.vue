<!-- filepath: frontend/src/components/Register.vue -->
<template>
  <form @submit.prevent="register">
    <h2>Register</h2>
    <input v-model="name" type="text" placeholder="Name" required />
    <input v-model="email" type="email" placeholder="Email" required />
    <input v-model="password" type="password" placeholder="Password" required />
    <button type="submit">Register</button>
    <p v-if="error">{{ error }}</p>
  </form>
</template>

<script setup>
import { ref } from 'vue'
const name = ref('')
const email = ref('')
const password = ref('')
const error = ref('')

async function register() {
  error.value = ''
  try {
    const res = await fetch('http://localhost:8080/api/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: name.value, email: email.value, password: password.value })
    })
    const data = await res.json()
    if (res.ok) {
      // Save token, redirect, etc.
      localStorage.setItem('token', data.token)
      window.location.href = '/tasks'
    } else {
      error.value = data.message || 'Registration failed'
    }
  } catch (e) {
    error.value = 'Network error'
  }
}
</script>
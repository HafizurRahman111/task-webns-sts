<template>
  <div class="auth-buttons">
    <template v-if="!authStore.isAuthenticated">
      <router-link to="/login" class="auth-button login-button">
        <FontAwesomeIcon :icon="['fas', 'sign-in-alt']" />
        <span>Login</span>
      </router-link>
      <router-link to="/register" class="auth-button register-button">
        <FontAwesomeIcon :icon="['fas', 'user-plus']" />
        <span>Register</span>
      </router-link>
    </template>

    <template v-else>
      <router-link to="/dashboard" class="auth-button dashboard-button">
        <FontAwesomeIcon :icon="['fas', 'tachometer-alt']" />
        <span>Dashboard</span>
      </router-link>
      <button @click="handleLogout" class="auth-button logout-button">
        <FontAwesomeIcon :icon="['fas', 'sign-out-alt']" />
        <span>Logout</span>
      </button>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

const authStore = useAuthStore()
const router = useRouter()
const isLoading = ref(false)

const handleLogout = async () => {
  isLoading.value = true
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.auth-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.auth-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.3rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.2s ease;
  color: white;
  text-decoration: none;
}

.auth-button:hover {
  transform: translateY(-1px);
}

.auth-button:active {
  transform: translateY(0);
}

.login-button,
.dashboard-button {
  background-color: #2563eb;
}

.login-button:hover,
.dashboard-button:hover {
  background-color: #1d4ed8;
}

.register-button {
  background-color: #7c3aed;
}

.register-button:hover {
  background-color: #6d28d9;
}

.logout-button {
  background-color: #dc2626;
}

.logout-button:hover {
  background-color: #b91c1c;
}

/* Loading state */
.auth-button[disabled] {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
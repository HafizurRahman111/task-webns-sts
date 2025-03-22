<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-md py-2">
      <div class="container mx-auto flex justify-between items-center px-6">
        <!-- Logo -->
        <router-link to="/" class="text-xl md:text-2xl font-semibold hover:text-gray-200 transition duration-200">
          Support Ticketing System
        </router-link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-6">
          <!-- Show Dashboard and Logout buttons when authenticated -->
          <template v-if="isAuthenticated">
            <router-link to="/dashboard" class="hover:text-gray-300 transition duration-200">Dashboard</router-link>
            <button @click="logout" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition duration-200">
              Logout
            </button>
          </template>

          <!-- Show Login and Register buttons when not authenticated -->
          <template v-else>
            <div class="flex justify-center gap-4">
              <router-link to="/login"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition duration-200">
                <font-awesome-icon :icon="['fas', 'sign-in-alt']" />
                Login
              </router-link>
              <router-link to="/register"
                class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded transition duration-200">
                <font-awesome-icon :icon="['fas', 'user-plus']" />
                Register
              </router-link>
            </div>
          </template>
        </nav>

        <!-- Mobile Menu Button -->
        <button @click="toggleMenu" class="md:hidden text-white focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>
      </div>

      <!-- Mobile Navigation Menu -->
      <div v-if="isMenuOpen"
        class="md:hidden bg-blue-700 text-white p-4 space-y-4 transition-all duration-300 ease-in-out">
        <!-- Show Dashboard and Logout buttons when authenticated -->
        <template v-if="isAuthenticated">
          <router-link to="/dashboard"
            class="block py-2 hover:text-gray-300 transition duration-200">Dashboard</router-link>
          <button @click="logout"
            class="block w-full text-center bg-red-500 hover:bg-red-600 px-4 py-2 mt-2 rounded transition duration-200">
            Logout
          </button>
        </template>

        <!-- Show Login and Register buttons when not authenticated -->
        <template v-else>
          <router-link to="/login" class="block py-2 hover:text-gray-300 transition duration-200">Login</router-link>
          <router-link to="/register"
            class="block py-2 hover:text-gray-300 transition duration-200">Register</router-link>
        </template>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto p-6">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-600 to-gray-500 text-white py-6 mt-auto">
      <div class="container mx-auto px-6">
        <!-- Footer Content -->
        <div class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0">
          <!-- Copyright -->
          <p class="text-sm">
            &copy; {{ new Date().getFullYear() }} Support Ticketing System. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

// Refs for authentication and menu state
const isAuthenticated = ref(false); // Replace with actual authentication logic
const isMenuOpen = ref(false);

// Router instance
const router = useRouter();

// Toggle mobile menu
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

// Logout function
const logout = () => {
  // Add logout logic here
  isAuthenticated.value = false;
  router.push('/login');
};
</script>

<style scoped>
/* Add custom animations */
.router-link-active {
  /* @apply text-blue-500; */
  /* Active link color */
}

/* Fade-in animation for mobile menu */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
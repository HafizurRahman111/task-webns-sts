<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-md py-4">
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
            <div class="flex space-x-4">
              <router-link to="/login"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition duration-200">
                Login
              </router-link>
              <router-link to="/register"
                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded transition duration-200">
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
    <main class="flex-grow items-center justify-center mx-auto p-6">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-600 to-gray-500 text-white py-6 mt-auto">
      <div class="container mx-auto px-6">
        <!-- Footer Content -->
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <!-- Copyright -->
          <p class="text-sm">
            &copy; {{ new Date().getFullYear() }} Support Ticketing System. All rights reserved.
          </p>

          <!-- Social Links -->
          <div class="flex space-x-4">
            <a href="https://twitter.com" target="_blank" class="hover:text-gray-300 transition">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M22.23 5.924a8.212 8.212 0 01-2.357.646 4.115 4.115 0 001.804-2.27 8.221 8.221 0 01-2.606.996 4.103 4.103 0 00-7.094 2.803c0 .322.036.636.106.94a11.65 11.65 0 01-8.457-4.287 4.103 4.103 0 001.27 5.477 4.091 4.091 0 01-1.858-.513v.052a4.103 4.103 0 003.292 4.023 4.108 4.108 0 01-1.853.07 4.103 4.103 0 003.833 2.85 8.233 8.233 0 01-5.096 1.756c-.332 0-.66-.02-.98-.057a11.616 11.616 0 006.29 1.843c7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.531a8.298 8.298 0 002.047-2.124z" />
              </svg>
            </a>
            <a href="https://github.com" target="_blank" class="hover:text-gray-300 transition">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M12 2C6.477 2 2 6.477 2 12c0 4.418 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.699-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.464-1.11-1.464-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.088 2.91.832.091-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.03-2.682-.103-.253-.447-1.27.098-2.646 0 0 .84-.269 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.295 2.747-1.026 2.747-1.026.547 1.376.203 2.394.1 2.646.64.698 1.026 1.591 1.026 2.682 0 3.842-2.337 4.687-4.565 4.935.359.309.678.919.678 1.852 0 1.338-.012 2.419-.012 2.747 0 .267.18.577.688.48C19.137 20.166 22 16.418 22 12c0-5.523-4.477-10-10-10z" />
              </svg>
            </a>
            <a href="https://linkedin.com" target="_blank" class="hover:text-gray-300 transition">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M19 0H5a5 5 0 00-5 5v14a5 5 0 005 5h14a5 5 0 005-5V5a5 5 0 00-5-5zM8 19H5V8h3v11zM6.5 6.732a1.5 1.5 0 110-3 1.5 1.5 0 010 3zM20 19h-3v-5.604c0-3.368-4-3.113-4 0V19h-3V8h3v1.765c1.396-2.586 7-2.777 7 2.476V19z" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// No logic needed for the layout
</script>

<style scoped>
/* No custom CSS needed - Tailwind handles everything */
</style>
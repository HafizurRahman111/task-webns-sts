<template>
  <div class="dashboard-layout">
    <DashboardHeader :user="userDetails" :is-dropdown-open="isDropdownOpen" :user-initials="userInitials"
      @toggle-dropdown="toggleDropdown" @logout="handleLogout" @toggle-sidebar="toggleSidebar" />

    <div class="dashboard-content-container">
      <DashboardSidebar :is-open="isSidebarOpen" :user-role="userRole" />

      <main class="main-content-area" :class="{ 'sidebar-collapsed': !isSidebarOpen }">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import DashboardHeader from '@/components/layout/DashboardHeader.vue'
import DashboardSidebar from '@/components/layout/DashboardSidebar.vue'

const authStore = useAuthStore()
const router = useRouter()

// State
const isDropdownOpen = ref(false)
const isSidebarOpen = ref(window.innerWidth > 768)

// Computed
const userDetails = computed(() => authStore.user || {})
const userRole = computed(() => authStore.user?.role || 'user')
const userInitials = computed(() => {
  if (!userDetails.value?.name) return 'U'
  return userDetails.value.name
    .split(' ')
    .slice(0, 2)
    .map(name => name[0]?.toUpperCase() ?? '')
    .join('')
})

// Methods
const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const handleResize = () => {
  isSidebarOpen.value = window.innerWidth > 768
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push({ name: 'login' })
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

// Lifecycle
onMounted(async () => {
  try {
    if (!authStore.user) {
      await authStore.fetchUser()
    }
    window.addEventListener('resize', handleResize)
  } catch (error) {
    console.error('Failed to initialize user data:', error)
    router.push({ name: 'login' })
  }
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: var(--color-bg);
}

.dashboard-content-container {
  display: flex;
  flex: 1;
  padding-top: 1.5rem;
  position: relative;
  overflow: hidden;
}

.main-content-area {
  flex: 1;
  padding: 1rem;
  margin-left: 15rem;
  transition: margin-left 0.3s ease;
  min-height: calc(100vh - 3rem);
  overflow-y: auto;
  background-color: var(--color-bg);
}

.main-content-area.sidebar-collapsed {
  margin-left: 0;
}

@media (max-width: 768px) {
  .main-content-area {
    margin-left: 0;
    padding: 1rem;
  }
}

/* Transition effects */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
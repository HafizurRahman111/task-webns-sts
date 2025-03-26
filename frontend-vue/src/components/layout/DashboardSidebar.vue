<template>
  <aside class="dashboard-sidebar" :class="{ 'sidebar-collapsed': !isOpen }" aria-label="Main navigation">
    <nav class="sidebar-nav">
      <ul class="sidebar-menu">
        <SidebarItem to="/dashboard" icon="dashboard" label="Dashboard" />

        <SidebarItem v-if="userRole === 'admin'" to="/dashboard/users" icon="users" label="User Management" />

        <!-- Support Tickets with Submenu -->
        <li class="sidebar-group">
          <button class="sidebar-item" :class="{ 'active': isTicketsMenuOpen }" @click="toggleTicketsMenu"
            aria-haspopup="true" :aria-expanded="isTicketsMenuOpen">
            <span class="sidebar-icon">
              <Icon name="tickets" />
            </span>
            <span class="sidebar-label">Support Tickets</span>
            <span class="sidebar-chevron">
              <Icon name="chevron-down" :class="{ 'rotate-180': isTicketsMenuOpen }" />
            </span>
          </button>

          <transition enter-active-class="transition-all duration-200 ease-out"
            leave-active-class="transition-all duration-150 ease-in" enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-96" leave-from-class="opacity-100 max-h-96"
            leave-to-class="opacity-0 max-h-0">
            <ul v-show="isTicketsMenuOpen" class="sidebar-submenu">
              <SidebarItem to="/dashboard/tickets" icon="list" label="All Tickets" subitem />
              <SidebarItem to="/tickets/create" icon="plus" label="Create Ticket" subitem />
            </ul>
          </transition>
        </li>

        <SidebarItem to="/dashboard/chats" icon="chats" label="Live Chat" />

      </ul>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import SidebarItem from '@/components/layout/SidebarItem.vue'
import Icon from '@/components/ui/Icon.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: true
  },
  userRole: {
    type: String,
    default: 'user'
  }
})

const isTicketsMenuOpen = ref(false)

const toggleTicketsMenu = () => {
  isTicketsMenuOpen.value = !isTicketsMenuOpen.value
}
</script>

<style scoped>
.dashboard-sidebar {
  position: fixed;
  left: 0;
  top: 4rem;
  /* 64px - header height */
  bottom: 0;
  width: 16rem;
  /* 256px */
  background-color: #1f2937;
  /* gray-800 */
  color: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  z-index: 40;
  overflow-y: auto;
  transition: transform 0.3s ease;
}

.dashboard-sidebar.sidebar-collapsed {
  transform: translateX(-100%);
}

.sidebar-nav {
  padding: 1.5rem 0;
  /* 24px top/bottom, 0 left/right */
}

.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  /* 4px */
}

.sidebar-group {
  display: flex;
  flex-direction: column;
}

.sidebar-item {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.75rem 1.5rem;
  color: #d1d5db;
  /* gray-300 */
  background-color: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.sidebar-item:hover {
  background-color: #374151;
  /* gray-700 */
  color: white;
}

.sidebar-item.active {
  background-color: #374151;
  /* gray-700 */
  color: white;
}

.sidebar-icon {
  display: flex;
  margin-right: 0.75rem;
  /* 12px */
  width: 1.25rem;
  /* 20px */
  height: 1.25rem;
  /* 20px */
}

.sidebar-label {
  flex-grow: 1;
  font-size: 0.875rem;
  /* 14px */
  line-height: 1.25rem;
  /* 20px */
}

.sidebar-chevron {
  transition: transform 0.2s ease;
  width: 1rem;
  /* 16px */
  height: 1rem;
  /* 16px */
}

.rotate-180 {
  transform: rotate(180deg);
}

.sidebar-submenu {
  padding-left: 2.5rem;
  /* 40px */
  overflow: hidden;
}

@media (max-width: 768px) {
  .dashboard-sidebar {
    transform: translateX(-100%);
  }

  .dashboard-sidebar.sidebar-open {
    transform: translateX(0);
  }
}

/* Scrollbar styling */
.dashboard-sidebar::-webkit-scrollbar {
  width: 6px;
}

.dashboard-sidebar::-webkit-scrollbar-track {
  background: #374151;
  /* gray-700 */
}

.dashboard-sidebar::-webkit-scrollbar-thumb {
  background: #4b5563;
  /* gray-600 */
  border-radius: 3px;
}

.dashboard-sidebar::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
  /* gray-500 */
}
</style>
<template>
  <aside class="dashboard-sidebar" :class="{ 'sidebar-collapsed': !isOpen }" aria-label="Main navigation">
    <nav class="sidebar-nav">
      <ul class="sidebar-menu">
        <SidebarItem to="/dashboard" icon="fa fa-dashboard" label="Dashboard" />
        <SidebarItem v-if="userRole === 'admin'" to="/dashboard/users" icon="fa fa-users" label="User Management" />
        <SidebarItem to="/dashboard/tickets" icon="fa fa-life-ring" label="Support Tickets" />
        <SidebarItem to="/dashboard/chats" icon="fa fa-comments" label="Live Chat" />
      </ul>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue';
import SidebarItem from '@/components/layout/SidebarItem.vue';
import Icon from '@/components/ui/Icon.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: true,
  },
  userRole: {
    type: String,
    default: 'user',
  },
});

const isTicketsMenuOpen = ref(false);

const toggleTicketsMenu = () => {
  isTicketsMenuOpen.value = !isTicketsMenuOpen.value;
};
</script>

<style scoped>
.dashboard-sidebar {
  position: fixed;
  left: 0;
  top: 3rem;
  bottom: 0;
  width: 16rem;
  background-color: #1f2937;
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
}

.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
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
  background-color: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.sidebar-item:hover {
  background-color: #374151;
  color: white;
}

.sidebar-item.active {
  background-color: #374151;
  color: white;
}

.sidebar-icon {
  display: flex;
  margin-right: 0.75rem;
  width: 1.25rem;
  height: 1.25rem;
}

.sidebar-label {
  flex-grow: 1;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.sidebar-chevron {
  transition: transform 0.2s ease;
  width: 1rem;
  height: 1rem;
}

.rotate-180 {
  transform: rotate(180deg);
}

.sidebar-submenu {
  padding-left: 2.5rem;
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
}

.dashboard-sidebar::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 3px;
}

.dashboard-sidebar::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
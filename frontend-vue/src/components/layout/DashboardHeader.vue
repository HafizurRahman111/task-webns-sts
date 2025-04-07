<template>
  <header class="dashboard-header">
    <div class="header-container">
      <!-- Logo/Title -->
      <h1 class="header-logo">
        <router-link to="/dashboard" class="logo-link">
          <span class="logo-icon"></span>
          <span class="logo-text"> Dashboard</span>
        </router-link>
      </h1>

      <!-- User Controls -->
      <div class="user-controls">
        <!-- Notification Bell -->
        <button class="notification-button" aria-label="Notifications">
          <BellIcon class="icon" />
          <span class="notification-badge">3</span>
        </button>

        <!-- User Info -->
        <div v-if="user.name" class="user-info">
          <span class="user-greeting">Welcome back, {{ user.name }}</span>
          <span class="user-role-badge">{{ formattedRole }}</span>
        </div>

        <!-- Profile Dropdown -->
        <div class="profile-dropdown" ref="dropdownRef">
          <button @click.stop="toggleDropdown" class="profile-button" aria-haspopup="true"
            :aria-expanded="isDropdownOpen" aria-label="User menu">
            <span class="profile-avatar">
              {{ userInitials }}
            </span>
            <ChevronIcon :class="['chevron-icon', { 'rotate-180': isDropdownOpen }]" aria-hidden="true" />
          </button>

          <Transition name="dropdown-fade">
            <div v-if="isDropdownOpen" class="dropdown-menu" role="menu">
              <div class="dropdown-header">
                <span class="profile-avatar-large">{{ userInitials }}</span>
                <div class="user-details">
                  <span class="user-name">{{ user.name }}</span>
                  <span class="user-email">{{ user.email }}</span>
                </div>
              </div>
              <div class="dropdown-divider"></div>
              <router-link to="/profile" class="dropdown-item" role="menuitem">
                <UserIcon class="dropdown-icon" />
                <span>My Profile</span>
              </router-link>
              <router-link to="/settings" class="dropdown-item" role="menuitem">
                <SettingsIcon class="dropdown-icon" />
                <span>Settings</span>
              </router-link>
              <div class="dropdown-divider"></div>
              <button @click="handleLogout" class="dropdown-item logout-item" role="menuitem">
                <LogoutIcon class="dropdown-icon" />
                <span>Sign Out</span>
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { onClickOutside } from '@vueuse/core'
import ChevronIcon from '@/components/icons/ChevronIcon.vue'
import UserIcon from '@/components/icons/UserIcon.vue'
import LogoutIcon from '@/components/icons/LogoutIcon.vue'
import SettingsIcon from '@/components/icons/SettingsIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'

// Debug: Log component initialization
// console.log('[DashboardHeader] Component initializing')

const props = defineProps({
  user: {
    type: Object,
    default: () => ({
      name: '',
      role: '',
      email: ''
    }),
    validator: (value) => {
      // console.log('[DashboardHeader] Validating user prop:', value)
      return typeof value === 'object' && value !== null
    }
  },
  isDropdownOpen: {
    type: Boolean,
    default: false
  }
})

// Debug: Log props when they change
// watch(() => props.user, (newUser) => {
//   console.log('[DashboardHeader] User prop changed:', newUser)
// }, { deep: true })

// watch(() => props.isDropdownOpen, (newVal) => {
//   console.log('[DashboardHeader] Dropdown state changed:', newVal)
// })

const emit = defineEmits(['toggle-dropdown', 'logout'])

const dropdownRef = ref(null)

// Debug: Add computed property logging
const userInitials = computed(() => {
  const initials = props.user?.name
    ? props.user.name
      .split(' ')
      .slice(0, 2)
      .map(name => name[0].toUpperCase())
      .join('')
    : 'U'
  // console.log('[DashboardHeader] Computed initials:', initials)
  return initials
})

const formattedRole = computed(() => {
  const role = props.user?.role
    ? props.user.role
      .split('_')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ')
    : ''
  // console.log('[DashboardHeader] Computed role:', role)
  return role
})

const toggleDropdown = () => {
  // console.log('[DashboardHeader] Toggling dropdown')
  emit('toggle-dropdown')
}

const closeDropdown = () => {
  if (props.isDropdownOpen) {
    // console.log('[DashboardHeader] Closing dropdown')
    emit('toggle-dropdown')
  }
}

const handleLogout = () => {
  // console.log('[DashboardHeader] Logout initiated')
  closeDropdown()
  emit('logout')
}

onClickOutside(dropdownRef, () => {
  // console.log('[DashboardHeader] Clicked outside dropdown')
  closeDropdown()
})

// Debug: Log mounted lifecycle
// onMounted(() => {
//   console.log('[DashboardHeader] Component mounted with user data:', props.user)
//   console.log('[DashboardHeader] Initial dropdown state:', props.isDropdownOpen)
// })
</script>

<style scoped>
.dashboard-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 3rem;
  background-color: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  z-index: 50;
  backdrop-filter: blur(8px);
  background-color: rgba(255, 255, 255, 0.8);
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 100%;
  max-width: 100%;
  margin: 0 auto;
  padding: 0 1rem;
}

.header-logo .logo-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  text-decoration: none;
  transition: opacity 0.2s ease;
}

.header-logo .logo-link:hover {
  opacity: 0.9;
}

.logo-icon {
  font-size: 1.5rem;
}

.user-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.notification-button {
  position: relative;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.3rem;
  color: #6b7280;
  transition: color 0.2s ease;
}

.notification-button:hover {
  color: #4b5563;
}

.notification-badge {
  position: absolute;
  top: 0;
  right: 0;
  width: 1rem;
  height: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #ef4444;
  color: white;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-greeting {
  font-size: 0.75rem;
  color: #4b5563;
  font-weight: 500;
}

.user-role-badge {
  font-size: 0.6rem;
  padding: 0.2rem 0.7rem;
  background-color: #f3f4f6;
  color: #374151;
  border-radius: 9999px;
  font-weight: 500;
  text-transform: capitalize;
}

.profile-dropdown {
  position: relative;
}

.profile-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.2rem;
  transition: opacity 0.2s ease;
}

.profile-button:hover {
  opacity: 0.9;
}

.profile-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 9999px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  font-weight: 600;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.profile-avatar-large {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  font-weight: 600;
  font-size: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  flex-shrink: 0;
}

.chevron-icon {
  width: 1rem;
  height: 1rem;
  color: #6b7280;
  transition: transform 0.2s ease, color 0.2s ease;
}

.profile-button:hover .chevron-icon {
  color: #4b5563;
}

.chevron-icon.rotate-180 {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: calc(100% + 0.5rem);
  min-width: 16rem;
  background-color: white;
  border-radius: 0.75rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  z-index: 50;
}

.dropdown-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background-color: #f9fafb;
}

.user-details {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.user-name {
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-email {
  font-size: 0.8rem;
  color: #6b7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-divider {
  height: 1px;
  background-color: #e5e7eb;
  margin: 0.25rem 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.5rem 1rem;
  text-align: left;
  font-size: 0.8rem;
  color: #374151;
  background: none;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f3f4f6;
}

.dropdown-icon {
  width: 1rem;
  height: 1rem;
  color: #9ca3af;
  flex-shrink: 0;
}

.dropdown-item.logout-item {
  color: #ef4444;
}

.dropdown-item.logout-item:hover {
  background-color: #fee2e2;
}

.dropdown-item.logout-item .dropdown-icon {
  color: #ef4444;
}

/* Animations */
.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: all 0.2s ease;
}

.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-0.5rem);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .header-container {
    padding: 0 1rem;
  }

  .user-greeting {
    display: none;
  }

  .logo-text {
    display: none;
  }
}
</style>
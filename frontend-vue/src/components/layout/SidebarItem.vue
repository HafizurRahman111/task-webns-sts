<template>
    <li>
      <router-link
        :to="to"
        class="sidebar-link"
        :class="{ 'sidebar-link-active': isActive }"
        @click.native="handleClick"
      >
        <DynamicIcon :name="icon" class="sidebar-icon" />
        <span class="sidebar-label">{{ label }}</span>
      </router-link>
    </li>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { useRoute } from 'vue-router'
  import DynamicIcon from '@/components/icons/DynamicIcon.vue'
  
  const props = defineProps({
    to: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: true
    }
  })
  
  const route = useRoute()
  const isActive = computed(() => route.path.startsWith(props.to))
  
  const handleClick = () => {
    // Optional: Add analytics or other side effects
  }
  </script>
  
  <style scoped>
  .sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #d1d5db; /* gray-300 */
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
  }
  
  .sidebar-link:hover {
    color: white;
    background-color: #374151; /* gray-700 */
  }
  
  .sidebar-link-active {
    color: white;
    background-color: #374151; /* gray-700 */
    font-weight: 500;
  }
  
  .sidebar-icon {
    width: 1.5rem;
    height: 1.5rem;
    flex-shrink: 0;
  }
  
  .sidebar-label {
    padding-left: 0.25rem;
    font-size: 0.875rem; /* text-sm */
    line-height: 1.25rem; /* leading-normal */
  }
  
  /* Focus styles for accessibility */
  .sidebar-link:focus-visible {
    outline: 2px solid #3b82f6; /* blue-500 */
    outline-offset: 2px;
  }
  </style>
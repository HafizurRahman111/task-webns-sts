<template>
    <component 
      :is="dynamicIcon" 
      class="icon" 
      :class="iconClass"
      aria-hidden="true"
    />
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  // Import all SVG icons
  import DashboardIcon from '@/assets/icons/dashboard.svg?component'
  import UsersIcon from '@/assets/icons/users.svg?component'
  import TicketsIcon from '@/assets/icons/tickets.svg?component'
  import ChatsIcon from '@/assets/icons/chats.svg?component'
  import ChevronDownIcon from '@/assets/icons/chevron-down.svg?component'
  import ListIcon from '@/assets/icons/list.svg?component'
  import PlusIcon from '@/assets/icons/plus.svg?component'
  import UserCheckIcon from '@/assets/icons/user-check.svg?component'
  import AnalyticsIcon from '@/assets/icons/analytics.svg?component'
  
  const props = defineProps({
    name: {
      type: String,
      required: true,
      validator: (value) => [
        'dashboard',
        'users',
        'tickets',
        'chats',
        'chevron-down',
        'list',
        'plus',
        'user-check',
        'analytics'
      ].includes(value)
    },
    iconClass: {
      type: String,
      default: ''
    }
  })
  
  const iconMap = {
    'dashboard': DashboardIcon,
    'users': UsersIcon,
    'tickets': TicketsIcon,
    'chats': ChatsIcon,
    'chevron-down': ChevronDownIcon,
    'list': ListIcon,
    'plus': PlusIcon,
    'user-check': UserCheckIcon,
    'analytics': AnalyticsIcon
  }
  
  const dynamicIcon = computed(() => iconMap[props.name])
  </script>
  
  <style scoped>
  .icon {
    display: inline-block;
    width: 1em;
    height: 1em;
    color: currentColor;
    vertical-align: middle;
    fill: currentColor;
    stroke: currentColor;
    transition: all 0.2s ease;
  }
  
  .icon.chevron-down {
    width: 0.8em;
    height: 0.8em;
  }
  </style>
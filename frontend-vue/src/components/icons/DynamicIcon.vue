<template>
    <component :is="iconComponent" class="w-6 h-6" />
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import ChevronIcon from './ChevronIcon.vue';
  import UserIcon from './UserIcon.vue';
  import LogoutIcon from './LogoutIcon.vue';
  
  const props = defineProps({
    name: String
  });
  
  const icons = {
    chevron: ChevronIcon,
    user: UserIcon,
    logout: LogoutIcon
  };
  
  const iconComponent = computed(() => icons[props.name] || null);
  </script>
  
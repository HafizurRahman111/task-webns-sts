<template>
    <span
      :class="[
        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
        roleClasses[role] || roleClasses.default,
        className,
      ]"
    >
      <slot>{{ role }}</slot>
    </span>
  </template>
  
  <script setup lang="ts">
  interface Props {
    role: string;
    className?: string;
  }
  
  const props = withDefaults(defineProps<Props>(), {
    className: '',
  });
  
  const roleClasses: Record<string, string> = {
    admin: 'bg-purple-100 text-purple-800',
    user: 'bg-green-100 text-green-800',
    guest: 'bg-yellow-100 text-yellow-800',
    moderator: 'bg-blue-100 text-blue-800',
    default: 'bg-gray-100 text-gray-800',
  };
  </script>
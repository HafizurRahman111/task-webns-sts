<template>
    <span
      :class="[
        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
        statusClasses[status],
        className,
      ]"
    >
      <span
        :class="[
          'w-2 h-2 rounded-full mr-1.5',
          dotClasses[status],
        ]"
      ></span>
      <slot>{{ status }}</slot>
    </span>
  </template>
  
  <script setup lang="ts">
  interface Props {
    status: 'active' | 'inactive' | 'pending' | 'warning' | 'error';
    className?: string;
  }
  
  const props = withDefaults(defineProps<Props>(), {
    className: '',
  });
  
  const statusClasses = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    warning: 'bg-orange-100 text-orange-800',
    error: 'bg-red-100 text-red-800',
  };
  
  const dotClasses = {
    active: 'bg-green-500',
    inactive: 'bg-gray-500',
    pending: 'bg-yellow-500',
    warning: 'bg-orange-500',
    error: 'bg-red-500',
  };
  </script>
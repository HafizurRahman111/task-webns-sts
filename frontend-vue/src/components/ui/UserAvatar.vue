<template>
    <div
      :class="[
        'rounded-full flex items-center justify-center bg-gray-200 overflow-hidden',
        sizeClasses[size],
        className,
      ]"
      :style="avatarStyle"
    >
      <img
        v-if="src"
        :src="src"
        :alt="alt"
        class="w-full h-full object-cover"
      />
      <span
        v-else
        :class="['font-medium text-gray-600', textSizeClasses[size]]"
      >
        {{ initials }}
      </span>
    </div>
  </template>
  
  <script setup lang="ts">
  import { computed } from 'vue';
  
  interface Props {
    src?: string;
    alt?: string;
    name?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl';
    className?: string;
    bgColor?: string;
  }
  
  const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    className: '',
    bgColor: '',
    alt: 'User avatar',
  });
  
  const sizeClasses = {
    sm: 'h-8 w-8',
    md: 'h-10 w-10',
    lg: 'h-12 w-12',
    xl: 'h-16 w-16',
  };
  
  const textSizeClasses = {
    sm: 'text-xs',
    md: 'text-sm',
    lg: 'text-base',
    xl: 'text-lg',
  };
  
  const initials = computed(() => {
    if (!props.name) return '?';
    const parts = props.name.split(' ');
    return parts
      .map(part => part[0]?.toUpperCase() || '')
      .slice(0, 2)
      .join('');
  });
  
  const avatarStyle = computed(() => ({
    backgroundColor: props.bgColor || '',
  }));
  </script>
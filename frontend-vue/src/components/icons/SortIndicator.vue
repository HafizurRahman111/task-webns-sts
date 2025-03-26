<template>
    <span class="ml-1 inline-flex flex-col">
      <span
        :class="[
          'h-2 w-2',
          direction === 'asc' ? 'text-gray-900' : 'text-gray-300',
        ]"
        aria-hidden="true"
      >
        &uarr;
      </span>
      <span
        :class="[
          'h-2 w-2',
          direction === 'desc' ? 'text-gray-900' : 'text-gray-300',
        ]"
        aria-hidden="true"
      >
        &darr;
      </span>
    </span>
  </template>
  
  <script setup lang="ts">
  interface Props {
    direction: 'asc' | 'desc' | 'none';
  }
  
  defineProps<Props>();
  </script>
<template>
    <div 
      class="feature-card"
      :class="{
        'hover:shadow-blue-100': color === 'blue',
        'hover:shadow-purple-100': color === 'purple',
        'hover:shadow-green-100': color === 'green',
        'hover:shadow-lg': true
      }"
      @mouseenter="hover = true"
      @mouseleave="hover = false"
    >
      <h3 class="feature-title" :class="{
        'text-blue-600': color === 'blue',
        'text-purple-600': color === 'purple',
        'text-green-600': color === 'green'
      }">
        {{ title }}
      </h3>
      <p class="feature-description">
        {{ description }}
      </p>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  
  defineProps({
    title: String,
    description: String,
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'purple', 'green'].includes(value)
    }
  })
  
  const hover = ref(false)
  </script>
  
  <style>
  .feature-card {
    padding: 1.5rem;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    transform: scale(1);
  }
  
  .feature-card:hover {
    transform: scale(1.03);
  }
  
  .feature-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }
  
  .feature-description {
    color: #374151;
  }
  
  @media (min-width: 768px) {
    .feature-card {
      padding: 2rem;
    }
  }
  
  /* Color-specific styles */
  .text-blue-600 {
    color: #2563eb;
  }
  .text-purple-600 {
    color: #9333ea;
  }
  .text-green-600 {
    color: #16a34a;
  }
  
  .hover\:shadow-blue-100:hover {
    --tw-shadow-color: #dbeafe;
    box-shadow: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -1px var(--tw-shadow-color);
  }
  .hover\:shadow-purple-100:hover {
    --tw-shadow-color: #f3e8ff;
    box-shadow: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -1px var(--tw-shadow-color);
  }
  .hover\:shadow-green-100:hover {
    --tw-shadow-color: #dcfce7;
    box-shadow: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -1px var(--tw-shadow-color);
  }
  </style>
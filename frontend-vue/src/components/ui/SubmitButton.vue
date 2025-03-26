<template>
    <button
      type="submit"
      class="submit-button"
      :disabled="loading"
    >
      <span v-if="!loading">{{ label }}</span>
      <LoadingSpinner v-else />
    </button>
  </template>
  
  <script setup>
  import LoadingSpinner from './LoadingSpinner.vue'
  
  defineProps({
    label: {
      type: String,
      default: 'Submit'
    },
    loading: Boolean
  })
  </script>
  
  <style scoped>
  .submit-button {
    width: 100%;
    padding: 0.5rem;
    background-color: rgb(49, 169, 238);
    color: white;
    border: none;
    border-radius: 0.375rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    height: 2.5rem;
  }
  
  .submit-button:hover {
    background-color: rgb(31, 126, 182);
  }
  
  .submit-button:disabled {
    background-color: rgba(175, 175, 175, 0.993);
    cursor: not-allowed;
  }
  </style>
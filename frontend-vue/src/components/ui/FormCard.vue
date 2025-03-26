<template>
    <section class="form-section">
      <div class="form-card">
        <h2 class="form-title">{{ title }}</h2>
        
        <form @submit.prevent="$emit('submit')" class="form-content">
          <slot></slot>
        </form>
        
        <p v-if="footerText" class="form-footer">
          <slot name="footer">{{ footerText }}</slot>
        </p>
        
        <p v-if="errorMessage" class="error-message text-center">
          {{ errorMessage }}
        </p>
      </div>
    </section>
  </template>
  
  <script setup>
  defineProps({
    title: String,
    footerText: String,
    errorMessage: String
  })
  
  defineEmits(['submit'])
  </script>
  
  <style scoped>
  .form-section {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 80vh;
    background-color: var(--bg-secondary);
    padding: 1rem;
  }
  
  .form-card {
    background-color: rgb(255, 255, 255);
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 28rem;
  }
  
  .form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: rgb(117, 117, 117);
    text-align: center;
    margin-bottom: 1.5rem;
  }
  
  .form-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .form-footer {
    text-align: center;
    margin-top: 1.5rem;
    color: rgb(73, 73, 73);
  }
  
  .error-message {
    color: rgb(240, 37, 37);
    font-size: 0.875rem;
    margin-top: 1rem;
    text-align: center;
  }
  </style>
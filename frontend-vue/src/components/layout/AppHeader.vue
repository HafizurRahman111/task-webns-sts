<template>
    <header class="app-header">
      <div class="header-container">
        <router-link to="/" class="app-logo">
          Support Ticketing System
        </router-link>
        <AuthButtons />
      </div>
    </header>
  </template>
  
  <script setup>
  import AuthButtons from '@/components/layout/AuthButtons.vue'
  </script>
  
  <style scoped>
  .app-header {
    background: linear-gradient(to right, #6b7280, #4b5563);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 0.5rem 0;
  }
  
  .header-container {
    width: 100%;
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1rem;
  }
  
  @media (min-width: 640px) {
    .header-container {
      padding: 0 1.5rem;
    }
  }
  
  .app-logo {
    font-size: 1.25rem;
    line-height: 1.75rem;
    font-weight: 600;
    color: white;
    transition: color 0.2s ease-in-out;
  }
  
  @media (min-width: 768px) {
    .app-logo {
      font-size: 1.5rem;
      line-height: 2rem;
    }
  }
  
  .app-logo:hover {
    color: #e5e7eb;
  }
  </style>
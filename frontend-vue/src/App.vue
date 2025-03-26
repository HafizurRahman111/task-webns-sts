<template>
  <router-view v-slot="{ Component, route }">
    <transition :name="route.meta.transition || 'fade'" mode="out-in">
      <suspense>
        <template #default>
          <component :is="Component" :key="route.meta.usePathKey ? route.path : undefined" />
        </template>
        <template #fallback>
          <app-loading />
        </template>
      </suspense>
    </transition>
  </router-view>
</template>

<script setup>
import AppLoading from '@/components/ui/AppLoading.vue'
</script>

<style>
/* CSS Variables */
:root {
  --transition-duration: 0.3s;
  --color-text: #333;
}

/* Base transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity var(--transition-duration) ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Layout-specific transitions */
.layout-slide-enter-active,
.layout-slide-leave-active {
  transition: transform var(--transition-duration) ease;
}

.layout-slide-enter-from {
  transform: translateX(-30px);
}

.layout-slide-leave-to {
  transform: translateX(30px);
}

/* Base styles */
body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  line-height: 1.5;
  color: var(--color-text);
  margin: 0;
  padding: 0;
  min-height: 100vh;
}

/* Import global styles after variables are defined */
@import '@/assets/main.css';
</style>
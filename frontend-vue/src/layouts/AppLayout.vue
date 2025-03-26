<template>
  <div class="app-layout">
    <AppHeader />
    <main class="app-main">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
import AppHeader from '@/components/layout/AppHeader.vue'
import AppFooter from '@/components/layout/AppFooter.vue'
</script>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f3f4f6; /* bg-gray-100 */
}

.app-main {
  flex: 1 0 auto;
  width: 100%;
  max-width: 1280px;
  margin: 0 auto;
  padding: 1rem;
}

@media (min-width: 640px) {
  .app-main {
    padding: 1.5rem;
  }
}
</style>
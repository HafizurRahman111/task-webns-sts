<template>
  <li>
    <RouterLink
      :to="to"
      class="sidebar-link"
      :class="{ 'sidebar-link-active': isActive }"
      @click="handleClick"
    >
      <i :class="icon" class="sidebar-icon"></i>
      <span class="sidebar-label">{{ label }}</span>
    </RouterLink>
  </li>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  to: { type: String, required: true },
  icon: { type: String, required: true },
  label: { type: String, required: true }
})

const route = useRoute()
const isActive = computed(() =>
  route.path === props.to || route.path.startsWith(props.to + '/')
)

const handleClick = () => {
  // Optional: Side effects like tracking
}
</script>

<style scoped>
.sidebar-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: #d1d5db; /* Tailwind gray-300 */
  border-radius: 0.5rem;
  text-decoration: none;
  transition: all 0.2s ease-in-out;
}

.sidebar-link:hover {
  color: #ffffff;
  background-color: #374151; /* Tailwind gray-700 */
}

.sidebar-link-active {
  color: #ffffff;
  background-color: #374151;
  font-weight: 600;
}

.sidebar-icon {
  font-size: 1.25rem;
  width: 1.5rem;
  height: 1.5rem;
  flex-shrink: 0;
}

.sidebar-label {
  font-size: 0.875rem; /* text-sm */
  line-height: 1.25rem;
}

.sidebar-link:focus-visible {
  outline: 2px solid #3b82f6; /* Tailwind blue-500 */
  outline-offset: 2px;
}
</style>

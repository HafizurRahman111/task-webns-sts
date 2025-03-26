<template>
    <div class="pagination-container">
      <div class="pagination-info">
        Showing {{ from }} to {{ to }} of {{ total }} entries
      </div>
      
      <div class="pagination-controls">
        <select
          v-model="localPerPage"
          class="page-size-select"
          @change="handlePerPageChange"
        >
          <option v-for="option in pageSizeOptions" :value="option" :key="option">
            {{ option }} per page
          </option>
        </select>
  
        <button
          class="pagination-btn"
          :disabled="currentPage === 1"
          @click="handlePageChange(currentPage - 1)"
        >
          <ChevronLeftIcon class="icon" />
        </button>
  
        <template v-for="(page, index) in visiblePages" :key="index">
          <button
            v-if="page === '...'"
            class="pagination-ellipsis"
            disabled
          >
            ...
          </button>
          <button
            v-else
            class="pagination-btn"
            :class="{ active: page === currentPage }"
            @click="handlePageChange(page)"
          >
            {{ page }}
          </button>
        </template>
  
        <button
          class="pagination-btn"
          :disabled="currentPage === lastPage"
          @click="handlePageChange(currentPage + 1)"
        >
          <ChevronRightIcon class="icon" />
        </button>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed } from 'vue'
  import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
  
  interface Props {
    currentPage: number
    perPage: number
    total: number
    lastPage: number
  }
  
  const props = defineProps<Props>()
  const emit = defineEmits(['page-change', 'per-page-change'])
  
  const localPerPage = ref(props.perPage)
  const pageSizeOptions = [5, 10, 20, 50]
  
  // Calculate visible range
  const from = computed(() => {
    return ((props.currentPage - 1) * props.perPage) + 1
  })
  
  const to = computed(() => {
    return Math.min(props.currentPage * props.perPage, props.total)
  })
  
  // Generate pagination buttons with ellipsis
  const visiblePages = computed(() => {
    const pages = []
    const current = props.currentPage
    const last = props.lastPage
    const delta = 2
  
    for (let i = 1; i <= last; i++) {
      if (i === 1 || i === last || (i >= current - delta && i <= current + delta)) {
        pages.push(i)
      } else if (i === current - delta - 1 || i === current + delta + 1) {
        pages.push('...')
      }
    }
  
    return pages
  })
  
  // Event handlers
  const handlePageChange = (page: number) => {
    if (page < 1 || page > props.lastPage) return
    emit('page-change', page)
  }
  
  const handlePerPageChange = () => {
    emit('per-page-change', localPerPage.value)
  }
  </script>
  
  <style scoped>
.pagination-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

@media (min-width: 640px) {
  .pagination-container {
    flex-direction: row;
    justify-content: space-between;
  }
}

.pagination-info {
  font-size: 0.875rem;
  color: #6b7280;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.page-size-select {
  padding: 0.25rem 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  margin-right: 0.5rem;
}

.pagination-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: all 0.15s ease;
}

.pagination-btn:hover:not(:disabled) {
  background-color: #f3f4f6;
  cursor: pointer;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-btn.active {
  background-color: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.pagination-btn.active:hover {
  background-color: #2563eb;
}

.pagination-ellipsis {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  color: #9ca3af;
}

.icon {
  width: 1rem;
  height: 1rem;
}
</style>
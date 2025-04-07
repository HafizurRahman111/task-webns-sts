<template>
    <button @click="$emit('click')"
        class="flex items-center px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-300 rounded-lg hover:bg-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>{{ label }}</span>
    </button>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: 'Back',
    },
})
</script>

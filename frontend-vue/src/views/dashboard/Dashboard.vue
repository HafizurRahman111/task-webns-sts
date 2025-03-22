<template>
    <div>
      <h1 class="text-3xl font-bold text-blue-600 mb-6">Dashboard Overview</h1>
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="p-6 bg-blue-500 text-white rounded-lg shadow-lg">
          <h2 class="text-xl font-bold">Total Tickets</h2>
          <p class="mt-2">123</p>
        </div>
        <div class="p-6 bg-purple-500 text-white rounded-lg shadow-lg">
          <h2 class="text-xl font-bold">Open Tickets</h2>
          <p class="mt-2">45</p>
        </div>
        <div class="p-6 bg-green-500 text-white rounded-lg shadow-lg">
          <h2 class="text-xl font-bold">Resolved Tickets</h2>
          <p class="mt-2">78</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  // No logic needed for the dashboard page
  </script>
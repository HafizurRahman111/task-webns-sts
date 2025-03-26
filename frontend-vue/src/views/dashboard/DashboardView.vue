<template>

  <!-- Debug Panel (visible in development) -->
  <!-- <div v-if="isDev" class="debug-panel">
    <button @click="showDebug = !showDebug" class="debug-toggle">
      {{ showDebug ? '▼ Hide Debug' : '▲ Show Debug' }}
    </button>
    <transition name="slide-down">
      <div v-if="showDebug" class="debug-content">
        <div class="debug-section">
          <h3>Application State</h3>
          <pre>{{ debugState }}</pre>
        </div>
        <div class="debug-section">
          <h3>API Responses</h3>
          <pre>{{ apiResponses }}</pre>
        </div>
        <div class="debug-section">
          <h3>Event Log</h3>
          <div class="event-log">
            <div v-for="(log, index) in eventLog" :key="index" class="log-entry">
              <span class="log-time">{{ log.timestamp }}</span>
              <span class="log-level" :class="log.level">{{ log.level.toUpperCase() }}</span>
              <span class="log-message">{{ log.message }}</span>
              <pre v-if="log.data" class="log-data">{{ log.data }}</pre>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div> -->

  <!-- Loading state -->
  <div v-if="loading" class="p-8 text-center">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
    <p class="mt-4 text-gray-600">Loading dashboard...</p>
  </div>

  <!-- Error state -->
  <div v-else-if="error" class="p-8 text-center text-red-500">
    <p class="text-lg font-semibold">Error loading dashboard</p>
    <p class="mt-2">{{ error.message }}</p>
    <div v-if="error.details" class="mt-4 p-3 bg-red-50 rounded-md text-left max-w-md mx-auto">
      <details>
        <summary class="text-sm font-medium cursor-pointer">Error Details</summary>
        <pre class="text-xs mt-2 overflow-auto">{{ error.details }}</pre>
      </details>
    </div>
    <button @click="fetchDashboardStats"
      class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
      Retry
    </button>
  </div>

  <!-- Main Content -->
  <div v-else class="p-4">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

    <!-- Cards Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div v-for="card in filteredCards" :key="card.title"
        class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-100"
        :title="`${card.title}: ${card.value}`">
        <div class="flex items-center">
          <div class="p-3 rounded-full mr-4" :class="card.bgColor">
            <component :is="card.icon" class="h-6 w-6" :class="card.iconColor" />
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">{{ card.title }}</h3>
            <p class="text-2xl font-bold mt-1" :class="card.textColor">
              {{ formatCardValue(card.value) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Tickets Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Recent Tickets</h3>
        <div class="flex items-center space-x-4">
          <button @click="refreshTickets" class="text-sm text-blue-600 hover:text-blue-800 flex items-center"
            :disabled="refreshingTickets" title="Refresh ticket list">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" :class="{ 'animate-spin': refreshingTickets }"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh
          </button>
          <router-link to="/tickets" class="text-sm text-blue-600 hover:text-blue-800 hover:underline"
            title="View all tickets">
            View All Tickets
          </router-link>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Subject</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Priority</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="ticket in recentTickets" :key="ticket.id"
              class="hover:bg-gray-50 transition-colors cursor-pointer" @click="viewTicket(ticket.id)">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ ticket.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ticket.subject || 'No subject' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span :class="`px-2 py-1 rounded-full text-xs font-semibold ${getPriorityClass(ticket.priority)}`">
                  {{ formatPriority(ticket.priority) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span :class="`px-2 py-1 rounded-full text-xs font-semibold ${getStatusClass(ticket.status)}`">
                  {{ formatStatus(ticket.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDateTime(ticket.created_at) }}
              </td>
            </tr>
            <tr v-if="recentTickets.length === 0 && !loadingTickets">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                No recent tickets found
              </td>
            </tr>
            <tr v-if="loadingTickets">
              <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                <div class="flex justify-center items-center">
                  <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500 mr-2"></div>
                  Loading tickets...
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import Chart from 'chart.js/auto';
import apiClient from '@/services/api.js';
import { useAuthStore } from '@/stores/auth.js';
import {
  TicketIcon,
  UserIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();
const authStore = useAuthStore();

// Environment
const isDev = import.meta.env.MODE === 'development';
const showDebug = ref(false);
const eventLog = ref([]);

// State
const loading = ref(true);
const loadingTickets = ref(false);
const refreshingTickets = ref(false);
const error = ref(null);
const chartTimeRange = ref('week');
const apiResponses = ref({});

// Dashboard data
const stats = ref({
  totalTickets: 0,
  openTickets: 0,
  inProgressTickets: 0,
  resolvedTickets: 0,
  closedTickets: 0,
  totalUsers: null,
});

const recentTickets = ref([]);
const ticketTrendData = ref({ labels: [], data: [] });

// Chart instances
const ticketTrendChart = ref(null);
const statusChart = ref(null);

// Logging function
const logEvent = (level, message, data = null) => {
  const timestamp = new Date().toISOString().slice(11, 23);
  eventLog.value.push({ timestamp, level, message, data });
  if (level === 'error') console.error(message, data);
  else if (level === 'warn') console.warn(message, data);
  else console.log(message, data);
};

// Debug state
const debugState = computed(() => ({
  auth: {
    isAuthenticated: authStore.isAuthenticated,
    user: authStore.user,
    role: authStore.user?.role
  },
  loading: loading.value,
  loadingTickets: loadingTickets.value,
  error: error.value,
  stats: stats.value,
  recentTicketsCount: recentTickets.value.length,
  chartTimeRange: chartTimeRange.value,
  ticketTrendDataPoints: ticketTrendData.value.data?.length || 0
}));

const hasTicketData = computed(() => {
  return stats.value.openTickets > 0 ||
    stats.value.inProgressTickets > 0 ||
    stats.value.resolvedTickets > 0 ||
    stats.value.closedTickets > 0;
});

// Card definitions with icons
const cardDefinitions = [
  {
    title: 'Total Tickets',
    value: computed(() => stats.value.totalTickets),
    icon: TicketIcon,
    bgColor: 'bg-blue-50',
    iconColor: 'text-blue-600',
    textColor: 'text-blue-600',
    show: true
  },
  {
    title: 'Open Tickets',
    value: computed(() => stats.value.openTickets),
    icon: ClockIcon,
    bgColor: 'bg-yellow-50',
    iconColor: 'text-yellow-600',
    textColor: 'text-yellow-600',
    show: true
  },
  {
    title: 'In Progress',
    value: computed(() => stats.value.inProgressTickets),
    icon: ArrowTrendingUpIcon,
    bgColor: 'bg-purple-50',
    iconColor: 'text-purple-600',
    textColor: 'text-purple-600',
    show: true
  },
  {
    title: 'Resolved',
    value: computed(() => stats.value.resolvedTickets),
    icon: CheckCircleIcon,
    bgColor: 'bg-green-50',
    iconColor: 'text-green-600',
    textColor: 'text-green-600',
    show: true
  },
  {
    title: 'Closed',
    value: computed(() => stats.value.closedTickets),
    icon: XCircleIcon,
    bgColor: 'bg-red-50',
    iconColor: 'text-red-600',
    textColor: 'text-red-600',
    show: true
  },
  {
    title: 'Total Users',
    value: computed(() => stats.value.totalUsers),
    icon: UserIcon,
    bgColor: 'bg-indigo-50',
    iconColor: 'text-indigo-600',
    textColor: 'text-indigo-600',
    show: computed(() => authStore.user?.role === 'admin')
  }
];

const filteredCards = computed(() => {
  return cardDefinitions.filter(card => card.show === true || (typeof card.show === 'function' ? card.show() : card.show));
});

const formatCardValue = (value) => {
  if (value === null || value === undefined) return 'N/A';
  return value;
};

// Fetch dashboard data from single endpoint
const fetchDashboardStats = async () => {
  try {
    loading.value = true;
    loadingTickets.value = true;
    error.value = null;
    logEvent('info', 'Fetching dashboard data...');

    const response = await apiClient.getStatistics('').catch(err => {
      logEvent('error', 'Failed to fetch stats', err);
      throw err;
    });

    apiResponses.value = response.data;

    // Validate response
    if (!response.data?.stats || !Array.isArray(response.data?.recentTickets)) {
      const err = new Error('Invalid response format');
      logEvent('error', err.message, response.data);
      throw err;
    }

    // Update reactive state
    stats.value = response.data.stats;
    // Sort recentTickets by ID in descending order
    recentTickets.value = response.data.recentTickets.sort((a, b) => b.id - a.id);

    // Generate simple trend data based on time range
    generateTrendData();

    logEvent('info', 'Dashboard data updated successfully');
    renderCharts();

  } catch (err) {
    const errorDetails = {
      message: err.response?.data?.message || err.message,
      code: err.response?.status,
      stack: err.stack,
      response: err.response?.data
    };

    error.value = {
      message: errorDetails.message || 'Failed to load dashboard data',
      details: errorDetails
    };

    logEvent('error', 'Dashboard load failed', errorDetails);

    // Reset data
    stats.value = {
      totalTickets: 0,
      openTickets: 0,
      inProgressTickets: 0,
      resolvedTickets: 0,
      closedTickets: 0,
      totalUsers: null,
    };
    recentTickets.value = [];
    ticketTrendData.value = { labels: [], data: [] };

  } finally {
    loading.value = false;
    loadingTickets.value = false;
    logEvent('info', 'Dashboard load completed');
  }
};

// Generate simple trend data since we don't have real trend data
const generateTrendData = () => {
  const days = chartTimeRange.value === 'week' ? 7 :
    chartTimeRange.value === 'month' ? 30 : 12;

  const labels = [];
  const data = [];

  for (let i = days - 1; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);

    labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));

    // Simple random data for demonstration
    data.push(Math.floor(Math.random() * 5));
  }

  ticketTrendData.value = { labels, data };
};

// Refresh dashboard data
const refreshTickets = async () => {
  try {
    refreshingTickets.value = true;
    logEvent('info', 'Refreshing dashboard data...');
    await fetchDashboardStats();
    logEvent('info', 'Dashboard data refreshed successfully');
  } catch (err) {
    logEvent('error', 'Failed to refresh dashboard data', err);
  } finally {
    refreshingTickets.value = false;
  }
};

// Chart rendering (same as before)
const renderCharts = () => {
  try {
    logEvent('info', 'Rendering charts...');

    if (ticketTrendChart.value) {
      ticketTrendChart.value.destroy();
      ticketTrendChart.value = null;
    }
    if (statusChart.value) {
      statusChart.value.destroy();
      statusChart.value = null;
    }

    // Ticket Trend Chart
    if (ticketTrendData.value.labels?.length) {
      const trendCtx = document.getElementById('ticketTrendChart')?.getContext('2d');
      if (trendCtx) {
        ticketTrendChart.value = new Chart(trendCtx, {
          type: 'line',
          data: {
            labels: ticketTrendData.value.labels,
            datasets: [{
              label: 'Tickets Created',
              data: ticketTrendData.value.data,
              backgroundColor: 'rgba(59, 130, 246, 0.05)',
              borderColor: 'rgba(59, 130, 246, 1)',
              borderWidth: 2,
              tension: 0.1,
              fill: true,
              pointBackgroundColor: 'rgba(59, 130, 246, 1)',
              pointRadius: 4,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                mode: 'index',
                intersect: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  drawBorder: false
                },
                ticks: {
                  stepSize: 1
                }
              },
              x: {
                grid: {
                  display: false
                }
              }
            }
          }
        });
        logEvent('info', 'Trend chart rendered');
      }
    }

    // Status Chart
    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    if (statusCtx) {
      statusChart.value = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
          datasets: [{
            data: [
              stats.value.openTickets,
              stats.value.inProgressTickets,
              stats.value.resolvedTickets,
              stats.value.closedTickets,
            ],
            backgroundColor: [
              'rgba(234, 179, 8, 0.7)',
              'rgba(168, 85, 247, 0.7)',
              'rgba(34, 197, 94, 0.7)',
              'rgba(239, 68, 68, 0.7)',
            ],
            borderColor: [
              'rgba(234, 179, 8, 1)',
              'rgba(168, 85, 247, 1)',
              'rgba(34, 197, 94, 1)',
              'rgba(239, 68, 68, 1)',
            ],
            borderWidth: 1,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          },
          cutout: '70%'
        }
      });
      logEvent('info', 'Status chart rendered');
    }
  } catch (err) {
    logEvent('error', 'Chart rendering failed', err);
  }
};

// Formatting helpers (same as before)
const formatStatus = (status) => {
  const statusMap = {
    open: 'Open',
    'in-progress': 'In Progress',
    resolved: 'Resolved',
    closed: 'Closed',
  };
  return statusMap[status] || status;
};

const formatPriority = (priority) => {
  const priorityMap = {
    low: 'Low',
    medium: 'Medium',
    high: 'High',
    critical: 'Critical'
  };
  return priorityMap[priority] || priority;
};

const getStatusClass = (status) => {
  const statusClasses = {
    open: 'bg-yellow-100 text-yellow-800',
    'in-progress': 'bg-purple-100 text-purple-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-red-100 text-red-800',
  };
  return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityClass = (priority) => {
  const priorityClasses = {
    low: 'bg-blue-100 text-blue-800',
    medium: 'bg-green-100 text-green-800',
    high: 'bg-yellow-100 text-yellow-800',
    critical: 'bg-red-100 text-red-800'
  };
  return priorityClasses[priority] || 'bg-gray-100 text-gray-800';
};

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A';
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (err) {
    logEvent('warn', 'Failed to format date', { dateString, error: err.message });
    return 'Invalid date';
  }
};

// Navigation
const viewTicket = (id) => {
  logEvent('info', `Navigating to ticket #${id}`);
  router.push(`/tickets/${id}`);
};

// Watch for time range changes
watch(chartTimeRange, () => {
  logEvent('info', `Chart time range changed to ${chartTimeRange.value}`);
  generateTrendData();
  renderCharts();
});

// Initial fetch
onMounted(() => {
  logEvent('info', 'Dashboard component mounted');
  fetchDashboardStats();
});

// Clean up
onBeforeUnmount(() => {
  logEvent('info', 'Dashboard component unmounting');
  if (ticketTrendChart.value) {
    ticketTrendChart.value.destroy();
    ticketTrendChart.value = null;
  }
  if (statusChart.value) {
    statusChart.value.destroy();
    statusChart.value = null;
  }
});
</script>

<style scoped>
/* Custom styles */
.h-64 {
  height: 16rem;
}

/* Smooth transitions */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Hover effects */
.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Table row hover */
tbody tr {
  transition: background-color 0.2s ease;
}

/* Card hover effect */
.bg-white:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Debug panel styles */
.debug-panel {
  position: fixed;
  bottom: 0;
  right: 0;
  left: 0;
  z-index: 1000;
  background: rgba(0, 0, 0, 0.9);
  color: white;
  font-family: monospace;
  font-size: 0.8rem;
  border-top: 1px solid #333;
  max-height: 50vh;
  overflow: hidden;
}

.debug-toggle {
  background: #4f46e5;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  width: 100%;
  text-align: left;
}

.debug-content {
  padding: 0.5rem;
  overflow-y: auto;
  max-height: 45vh;
}

.debug-section {
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #333;
}

.debug-section h3 {
  font-weight: bold;
  margin-bottom: 0.5rem;
  color: #60a5fa;
}

.debug-section pre {
  white-space: pre-wrap;
  word-break: break-all;
  background: #111;
  padding: 0.5rem;
  border-radius: 0.25rem;
  max-height: 200px;
  overflow-y: auto;
}

.event-log {
  font-family: monospace;
}

.log-entry {
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #333;
}

.log-time {
  color: #a78bfa;
  margin-right: 1rem;
}

.log-level {
  font-weight: bold;
  margin-right: 1rem;
  padding: 0.1rem 0.3rem;
  border-radius: 0.2rem;
}

.log-level.info {
  color: #6ee7b7;
}

.log-level.warn {
  color: #fcd34d;
}

.log-level.error {
  color: #fca5a5;
}

.log-message {
  color: #e5e7eb;
}

.log-data {
  margin-top: 0.3rem;
  padding-left: 1rem;
  color: #9ca3af;
  font-size: 0.7rem;
}

/* Transition animations */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
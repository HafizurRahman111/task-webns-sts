<template>
    <div class="ticket-management">
        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading tickets...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-8 text-center text-red-500">
            <p class="text-lg font-semibold">Error loading tickets</p>
            <p class="mt-2">{{ error.message }}</p>
            <button @click="fetchTickets"
                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                Retry
            </button>
        </div>

        <!-- Main Content -->
        <div v-else class="p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-800">
                    Tickets List
                </h2>
                <div class="flex flex-wrap items-center gap-4 justify-end">
                    <select v-model="perPage" @change="fetchTickets(1)"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="25">Show 25</option>
                        <option value="50">Show 50</option>
                        <option value="100">Show 100</option>
                        <option value="-1">Show All</option>
                    </select>
                    <input v-model="searchQuery" type="text" placeholder="Search tickets..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button @click="createTicket"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        Add New
                    </button>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>

                                <th v-for="column in columns" :key="column.key"
                                    class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    @click="column.sortable ? sortBy(column.key) : null"
                                    :class="{ 'hidden': column.adminOnly && !isAdmin }">
                                    <div class="flex items-center">
                                        {{ column.label }}
                                        <span v-if="sortKey === column.key" class="ml-1">
                                            {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                        </span>
                                    </div>
                                </th>
                                <th
                                    class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="ticket in paginatedTickets" :key="ticket.id"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">#{{ ticket.id
                                }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ ticket.subject }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                    <span
                                        :class="`px-2 py-1 rounded-full text-xs font-semibold ${getCategoryClass(ticket.category)}`">
                                        {{ ticket.category }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                    <span
                                        :class="`px-2 py-1 rounded-full text-xs font-semibold ${getPriorityClass(ticket.priority)}`">
                                        {{ ticket.priority }}
                                    </span>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500">
                                    <span
                                        :class="`px-2 py-1 rounded-full text-xs font-semibold ${getStatusClass(ticket.status)}`">
                                        {{ ticket.status }}
                                    </span>
                                </td>

                                <td v-if="isAdmin" class="px-4 py-4 whitespace-nowrap text-xs text-gray-700">{{
                                    ticket.user.name }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-700">{{
                                    formatDate(ticket.created_at) }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <button @click="viewTicket(ticket.id)"
                                            class="px-3 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 flex items-center">
                                            View
                                        </button>
                                        <button @click="updateTicket(ticket.id)"
                                            class="px-3 py-1 text-sm font-semibold text-yellow-600 bg-yellow-100 rounded-lg hover:bg-yellow-200 flex items-center">
                                            Edit
                                        </button>
                                        <button @click="confirmDelete(ticket)"
                                            class="px-3 py-1 text-sm font-semibold text-red-600 bg-red-100 rounded-lg hover:bg-red-200 flex items-center">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredTickets.length === 0 && !loading">
                                <td colspan="8" class="px-6 py-2 text-center text-sm text-gray-500">
                                    No tickets found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination"
                class="mt-6 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                <span class="text-sm text-gray-600">
                    Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} to
                    {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of
                    {{ pagination.total }} results
                </span>
                <div class="flex items-center space-x-2">
                    <button :disabled="!pagination.prev_page_url" @click="fetchTickets(pagination.current_page - 1)"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button :disabled="!pagination.next_page_url" @click="fetchTickets(pagination.current_page + 1)"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Are you sure you want to delete this ticket?</h3>
                <div class="flex justify-end space-x-4">
                    <button @click="showConfirmationModal = false"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button @click="deleteTicket"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div v-if="toast.show"
            class="fixed bottom-4 right-4 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow-lg animate-fade-in">
            {{ toast.message }}
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import apiClient from '@/services/api.js';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

// Reactive state
const tickets = ref([]);
const loading = ref(true);
const error = ref(false);
const pagination = ref(null);
const searchQuery = ref('');
const sortKey = ref('id');
const sortOrder = ref('desc'); // Default to descending
const perPage = ref(10);
const showConfirmationModal = ref(false);
const ticketToDelete = ref(null);
const toast = ref({ show: false, message: '', type: 'success' });

// Auth check
const isAdmin = computed(() => authStore.user?.role === 'admin');

// Table columns
const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'subject', label: 'Subject', sortable: true },
    { key: 'category', label: 'Category', sortable: true },
    { key: 'priority', label: 'Priority', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'user', label: 'User ', sortable: true, adminOnly: true },
    { key: 'created_at', label: 'Created', sortable: true }
];

// Create Ticket
const createTicket = () => {
    router.push({ name: 'TicketCreate' });
};

// Fetch tickets
const fetchTickets = async (page = 1) => {
    loading.value = true;
    error.value = null; // Reset error state

    const params = {
        page,
        per_page: perPage.value,
        search: searchQuery.value,
        sort_by: sortKey.value,
        sort_order: sortOrder.value
    }

    try {
        const response = await apiClient.getTickets('', { params })
        tickets.value = response.data.data.data;
        pagination.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            prev_page_url: response.data.data.prev_page_url,
            next_page_url: response.data.data.next_page_url,
        };
    } catch (err) {
        console.error('Failed to fetch tickets:', err);
        error.value = true;
    } finally {
        loading.value = false;
    }
};

// Watch for changes in perPage and refetch data
watch(perPage, () => {
    fetchTickets(1);
});

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

// Category-based styling
const getCategoryClass = (category) => {
    const categoryClasses = {
        technical: 'bg-blue-100 text-blue-600',
        billing: 'bg-green-100 text-green-600',
        general: 'bg-gray-100 text-gray-600',
    };
    return categoryClasses[category] || 'bg-gray-100 text-gray-600';
};

// Priority-based styling
const getPriorityClass = (priority) => {
    const priorityClasses = {
        high: 'bg-red-100 text-red-600',
        medium: 'bg-yellow-100 text-yellow-600',
        low: 'bg-green-100 text-green-600',
    };
    return priorityClasses[priority] || 'bg-gray-100 text-gray-600';
};

// Status-based styling
const getStatusClass = (status) => {
    const statusClasses = {
        open: 'bg-blue-100 text-blue-600',
        'in-progress': 'bg-yellow-100 text-yellow-600',
        resolved: 'bg-green-100 text-green-600',
        closed: 'bg-gray-100 text-gray-600',
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-600';
};

// Sort tickets by column
const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
    fetchTickets(pagination.value.current_page); // Fetch tickets after sorting
};

// Filtered and sorted tickets
const filteredTickets = computed(() => {
    let filtered = tickets.value;

    // Search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (ticket) =>
                ticket.subject.toLowerCase().includes(query) ||
                ticket.category.toLowerCase().includes(query) ||
                ticket.priority.toLowerCase().includes(query) ||
                ticket.status.toLowerCase().includes(query) ||
                (ticket.user?.name && ticket.user.name.toLowerCase().includes(query))
        );
    }

    // Sorting
    if (sortKey.value) {
        filtered = filtered.sort((a, b) => {
            if (a[sortKey.value] < b[sortKey.value]) return sortOrder.value === 'asc' ? -1 : 1;
            if (a[sortKey.value] > b[sortKey.value]) return sortOrder.value === 'asc' ? 1 : -1;
            return 0;
        });
    }

    return filtered;
});

// Paginated tickets
const paginatedTickets = computed(() => {
    if (!pagination.value) return filteredTickets.value;

    const start = (pagination.value.current_page - 1) * pagination.value.per_page;
    const end = start + pagination.value.per_page;
    return filteredTickets.value.slice(start, end);
});
// View Ticket
const viewTicket = (ticketId) => {
    router.push({ name: 'TicketDetails', params: { id: ticketId } });
};

// Edit Ticket
const updateTicket = (ticketId) => {
    router.push({ name: 'TicketEdit', params: { id: ticketId } });
};

// Confirm delete
const confirmDelete = (ticket) => {
    ticketToDelete.value = ticket;
    showConfirmationModal.value = true;
};

// Delete ticket
const deleteTicket = async () => {
    try {
        await apiClient.deleteTicket(ticketToDelete.value.id);
        showConfirmationModal.value = false;
        showToast('Ticket deleted successfully!');
        fetchTickets(pagination.value.current_page);
    } catch (err) {
        console.error('Failed to delete ticket:', err);
        showToast('Failed to delete ticket. Please try again.', 'error');
    }
};

// Show toast notification
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Fetch tickets on mount
onMounted(() => {
    fetchTickets();
});
</script>

<style scoped>
.ticket-management {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

/* Responsive table styles */
@media (max-width: 640px) {
    table {
        display: block;
    }

    thead {
        display: none;
    }

    tbody {
        display: block;
    }

    tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    td {
        display: block;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        border-bottom: 1px solid #e5e7eb;
    }

    td:last-child {
        border-bottom: none;
    }

    td:before {
        content: attr(data-label);
        font-weight: 500;
        color: #6b7280;
        display: block;
        margin-bottom: 0.25rem;
    }

    td[data-label] {
        display: flex;
        justify-content: space-between;
    }

    td[data-label]:before {
        margin-right: 1rem;
    }
}

/* Additional styles for buttons, tables, and other elements can be added here */
</style>
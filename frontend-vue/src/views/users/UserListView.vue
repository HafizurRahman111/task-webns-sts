<template>
  <div class="user-management">
    <!-- Loading state -->
    <div v-if="loading" class="p-8 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-4 text-gray-600">Loading users...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="p-8 text-center text-red-500">
      <p class="text-lg font-semibold">Error loading users</p>
      <p class="mt-2">{{ error.message }}</p>
      <button @click="fetchUsers"
        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
        Retry
      </button>
    </div>

    <!-- Main Content -->
    <div v-else class="p-4">
      <!-- Header Row: Title Left, Search Right -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <!-- Title -->
        <h2 class="text-2xl font-bold text-gray-800">User Management</h2>

        <!-- Search Input + Button -->
        <div class="flex items-center gap-2 w-full md:w-auto">
          <input v-model="searchQuery" @input="searchUsers" type="text" placeholder="Search users..."
            class="w-full md:w-64 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <button @click="searchUsers" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>

      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th @click="sort('name')" :class="{ active: sortField === 'name' }">
                User
                <span class="sort-icon">{{ getSortIcon('name') }}</span>
              </th>
              <th @click="sort('email')" :class="{ active: sortField === 'email' }">
                Email
                <span class="sort-icon">{{ getSortIcon('email') }}</span>
              </th>
              <th>Phone</th>
              <th @click="sort('role')" :class="{ active: sortField === 'role' }">
                Role
                <span class="sort-icon">{{ getSortIcon('role') }}</span>
              </th>
              <th @click="sort('created_at')" :class="{ active: sortField === 'created_at' }">
                Joined
                <span class="sort-icon">{{ getSortIcon('created_at') }}</span>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading" class="loading-row">
              <td colspan="6">
                <div class="loading-spinner"></div>
                Loading users...
              </td>
            </tr>
            <tr v-else-if="!users.length" class="empty-row">
              <td colspan="6">
                <div class="empty-icon">📭</div>
                No users found
              </td>
            </tr>
            <tr v-for="user in users" :key="user.id" class="user-row">
              <td>
                <div class="user-info">
                  <div class="user-avatar">{{ user.name.charAt(0) }}</div>
                  <div class="user-details">
                    <div class="user-name">{{ user.name }}</div>
                    <div class="user-email">{{ user.email }}</div>
                  </div>
                </div>
              </td>
              <td>{{ user.email }}</td>
              <td>{{ user.phone || '-' }}</td>
              <td>
                <span class="role-badge" :class="user.role">{{ user.role }}</span>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td>
                <button class="delete-btn" @click="deleteUser(user)" :disabled="user.role === 'admin'"
                  :title="user.role === 'admin' ? 'Cannot delete admin' : 'Delete user'">
                  🗑️
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button @click="prevPage" :disabled="page === 1" class="pagination-btn">
          &larr; Previous
        </button>
        <span class="page-info">Page {{ page }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="page >= totalPages" class="pagination-btn">
          Next &rarr;
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import apiClient from '@/services/api.js';

export default {
  setup() {
    // Data
    const users = ref([]);
    const loading = ref(true);
    const searchQuery = ref('');
    const sortField = ref('created_at');
    const sortDirection = ref('desc');
    const page = ref(1);
    const perPage = ref(10);
    const total = ref(0);
    const error = ref(null);

    // Computed
    const totalPages = computed(() => Math.ceil(total.value / perPage.value));

    // Methods
    const fetchUsers = async () => {
      loading.value = true;
      error.value = null;
      try {
        const response = await apiClient.getUsers({
          page: page.value,
          perPage: perPage.value,
          search: searchQuery.value,
          sortField: sortField.value,
          sortDirection: sortDirection.value,
        });

        users.value = response.data.data.data.sort((a, b) => b.id - a.id);

        total.value = response.data.data.total;
      } catch (err) {
        error.value = { message: "Error fetching users", details: err };
        console.error("Error fetching users:", err);
      } finally {
        loading.value = false;
      }
    };

    const searchUsers = () => {
      page.value = 1;
      fetchUsers();
    };

    const sort = (field) => {
      if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortField.value = field;
        sortDirection.value = 'asc';
      }
      fetchUsers();
    };

    const getSortIcon = (field) => {
      if (sortField.value !== field) return '⇅';
      return sortDirection.value === 'asc' ? '↑' : '↓';
    };

    const deleteUser = async (user) => {
      if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        try {
          await apiClient.deleteUser(user.id);
          fetchUsers();
        } catch (err) {
          console.error("Error deleting user:", err);
        }
      }
    };

    const nextPage = () => {
      if (page.value < totalPages.value) {
        page.value++;
        fetchUsers();
      }
    };

    const prevPage = () => {
      if (page.value > 1) {
        page.value--;
        fetchUsers();
      }
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    };

    // Lifecycle
    onMounted(fetchUsers);

    return {
      users,
      loading,
      searchQuery,
      page,
      totalPages,
      sortField,
      sortDirection,
      fetchUsers,
      searchUsers,
      sort,
      getSortIcon,
      deleteUser,
      nextPage,
      prevPage,
      formatDate,
      error,
    };
  },
};
</script>

<style scoped>
.user-management {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #333;
}

/* Header & Search */
.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.page-title {
  margin: 0;
  font-size: 2rem;
  font-weight: 600;
  color: #2c3e50;
}

.search-container {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.search-input {
  padding: 0.75rem 1rem;
  border: none;
  outline: none;
  font-size: 1rem;
  min-width: 250px;
}

.search-btn {
  background: #4CAF50;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.search-btn:hover {
  background: #3e8e41;
}

/* Table */
.table-container {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 2rem;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th {
  padding: 1rem;
  text-align: left;
  background: #f8f9fa;
  font-weight: 600;
  color: #495057;
  position: relative;
  cursor: pointer;
}

.user-table th:hover {
  background: #e9ecef;
}

.user-table th.active {
  background: #e9ecef;
  color: #2c3e50;
}

.sort-icon {
  margin-left: 0.5rem;
  font-size: 0.9rem;
}

.user-table td {
  padding: 1rem;
  border-top: 1px solid #e9ecef;
  vertical-align: middle;
}

/* Table Rows */
.loading-row td,
.empty-row td {
  text-align: center;
  padding: 2rem;
  color: #6c757d;
}

.loading-spinner {
  display: inline-block;
  width: 1.5rem;
  height: 1.5rem;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #4CAF50;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.5rem;
  vertical-align: middle;
}

.empty-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 0.5rem;
}

.user-row:hover {
  background-color: #f8f9fa;
}

/* User Info */
.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: #4CAF50;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
}

.user-details {
  line-height: 1.4;
}

.user-name {
  font-weight: 500;
}

.user-email {
  font-size: 0.85rem;
  color: #6c757d;
}

/* Role Badge */
.role-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 1rem;
  font-size: 0.85rem;
  font-weight: 500;
}

.role-badge.admin {
  background: #ffe0e0;
  color: #dc3545;
}

.role-badge.user {
  background: #e0f0ff;
  color: #0d6efd;
}

.role-badge.manager {
  background: #fff3cd;
  color: #ffc107;
}

/* Buttons */
.delete-btn {
  background: none;
  border: none;
  color: #dc3545;
  cursor: pointer;
  font-size: 1.2rem;
  padding: 0.5rem;
  border-radius: 4px;
  transition: all 0.2s;
}

.delete-btn:hover:not(:disabled) {
  background: #ffe0e0;
}

.delete-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  color: #6c757d;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 1.5rem;
}

.pagination-btn {
  padding: 0.5rem 1rem;
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background: #f8f9fa;
  border-color: #adb5bd;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.9rem;
  color: #6c757d;
}

/* Animations */
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
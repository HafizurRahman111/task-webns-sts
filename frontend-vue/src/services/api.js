import axios from 'axios';

// Create axios instance
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  withCredentials: true,
  timeout: 50000
});


// Request interceptor
apiClient.interceptors.request.use(config => {
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type'];
  }
  // Handle regular data objects
  else if (typeof config.data === 'object' && config.data !== null) {
    config.headers['Content-Type'] = 'application/json';
    config.data = JSON.stringify(config.data);
  }

  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => Promise.reject(error));

// Response interceptor
apiClient.interceptors.response.use(
  response => response,
  async error => {
    const originalRequest = error.config;

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        const refreshToken = localStorage.getItem('refreshToken');
        if (!refreshToken) throw error;

        const response = await apiClient.post('/refresh-token', {
          refresh_token: refreshToken
        });

        localStorage.setItem('token', response.data.access_token);
        if (response.data.refresh_token) {
          localStorage.setItem('refreshToken', response.data.refresh_token);
        }

        originalRequest.headers.Authorization = `Bearer ${response.data.access_token}`;
        return apiClient(originalRequest);
      } catch (refreshError) {
        localStorage.removeItem('token');
        localStorage.removeItem('refreshToken');
        window.location.href = '/login';
        return Promise.reject(refreshError);
      }
    }

    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('refreshToken');
      window.location.href = '/login';
    }

    return Promise.reject(error);
  }
);

export default {
  // Auth endpoints
  login(credentials) {
    return apiClient.post('/login', credentials);
  },
  logout() {
    return apiClient.post('/logout');
  },
  register(userData) {
    return apiClient.post('/register', userData);
  },
  getUser() {
    return apiClient.get('/user');
  },
  refreshToken() {
    return apiClient.post('/refresh-token');
  },

  // Ticket endpoints
  getStatistics(params = {}) {
    return apiClient.get('/stats', { params });
  },
  getTickets(params = {}) {
    return apiClient.get('/tickets', { params });
  },
  getTicket(id) {
    return apiClient.get(`/tickets/${id}`);
  },
  getAttachment(ticketId) {
    return apiClient.get(`/tickets/${ticketId}/attachment`, {
      responseType: 'blob'
    });
  },
  createTicket(ticketData) {
    return apiClient.post('/tickets', ticketData);
  },
  updateTicket(id, ticketData) {
    return apiClient.put(`/tickets/${id}`, ticketData);
  },
  deleteTicket(id) {
    return apiClient.delete(`/tickets/${id}`);
  },

  // Comment endpoints
  getComments(ticketId) {
    return apiClient.get(`/tickets/${ticketId}/comments`);
  },
  addComment(ticketId, commentData) {
    return apiClient.post(`/tickets/${ticketId}/comments`, commentData);
  },
  deleteComment(ticketId, commentId) {
    return apiClient.delete(`/tickets/${ticketId}/comments/${commentId}`);
  },

  // Chat endpoints
  getAllChats() {
    return apiClient.get(`chats`);
  },
  getChats(ticketId) {
    return apiClient.get(`/tickets/${ticketId}/chats`);
  },
  markAllAsReadTicket(ticketId) {
    return apiClient.get(`/chats/mark-read-all/${ticketId}`);
  },
  sendChatMessage(ticketId, message) {
    return apiClient.post(`/tickets/${ticketId}/chats`, { message });
  },

  // Admin endpoints
  getUsers(params = {}) {
    return apiClient.get('/admin/users', { params });
  },
  deleteUser(id) {
    return apiClient.delete(`/admin/users/${id}`);
  }
};
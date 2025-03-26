import { defineStore } from 'pinia';
import { ref, computed, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter();
  const user = ref(null);
  const authToken = ref(localStorage.getItem('token'));
  const refreshToken = ref(localStorage.getItem('refreshToken'));
  const loading = ref(false);
  const error = ref(null);
  let logoutTimer = null;

  const isAuthenticated = computed(() => !!authToken.value);
  const userRole = computed(() => user.value?.role || null);

  const setLogoutTimer = (expiresIn) => {
    clearTimeout(logoutTimer);
    if (expiresIn) {
      logoutTimer = setTimeout(() => logout(false), (expiresIn - 60) * 1000);
    }
  };

  const clearAuthData = () => {
    user.value = null;
    authToken.value = null;
    refreshToken.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('refreshToken');
    clearTimeout(logoutTimer);
  };

  const saveToken = (token, expiresIn) => {
    authToken.value = token;
    localStorage.setItem('token', token);
    setLogoutTimer(expiresIn);
  };

  const saveRefreshToken = (token) => {
    refreshToken.value = token;
    localStorage.setItem('refreshToken', token);
  };

  const fetchUser = async () => {
    try {
      loading.value = true;
      error.value = null;
      const { data } = await api.getUser('/user');
      user.value = data.data;
      isAuthenticated.value = true;
      return data;
    } catch (err) {
      error.value = err;
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const login = async (credentials) => {
    try {
      loading.value = true;
      error.value = null;
      const { data } = await api.login(credentials);
      saveToken(data.access_token, data.expires_in);
      saveRefreshToken(data.refresh_token);
      await fetchUser();
      router.push('/dashboard');
      return data;
    } catch (err) {
      error.value = err;
      clearAuthData();

      if (err.code === 'ERR_NETWORK') {
        err.message = 'Cannot connect to server. Please check your connection.';
      } else if (err.response?.status === 401) {
        err.message = 'Invalid credentials. Please try again.';
      }

      throw err;
    } finally {
      loading.value = false;
    }
  };

  const register = async (userData) => {
    try {
      loading.value = true;
      error.value = null;
      const { data } = await api.register(userData);
      return data;
    } catch (err) {
      error.value = err;

      if (err.response?.status === 422) {
        err.message = 'Validation failed: ' +
          Object.values(err.response.data.errors).join(' ');
      }

      throw err;
    } finally {
      loading.value = false;
    }
  };

  const logout = async (redirect = true) => {
    try {
      loading.value = true;
      await api.logout();
    } catch (err) {
      error.value = err;
      console.error('Logout error:', err);
    } finally {
      clearAuthData();
      loading.value = false;
      if (redirect) {
        router.push('/login');
      }
    }
  };

  const initialize = async () => {
    if (authToken.value && !user.value) {
      try {
        await fetchUser();
      } catch (err) {
        if (err.response?.status === 401) {
          clearAuthData();
        }
      }
    }
  };

  // Initialize auth store
  initialize();

  // Cleanup timer on unmount
  onUnmounted(() => {
    clearTimeout(logoutTimer);
  });

  return {
    user,
    authToken,
    refreshToken,
    isAuthenticated,
    userRole,
    loading,
    error,
    login,
    logout,
    register,
    fetchUser,
    initialize,
    saveToken,
    saveRefreshToken
  };
});
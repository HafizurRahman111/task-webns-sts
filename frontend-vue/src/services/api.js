import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://localhost:8000/api', // Laravel API URL
    headers: {
        'Content-Type': 'application/json',
    },
});

export default {
    getCsrfToken() {
        return apiClient.get('/csrf-token'); // Endpoint to fetch CSRF token
    },
    register(user) {
        return apiClient.post('/register', user);
    },
    login(credentials) {
        return apiClient.post('/login', credentials);
    },
    logout() {
        return apiClient.post('/logout');
    },
    getUser() {
        return apiClient.get('/user');
    },
};
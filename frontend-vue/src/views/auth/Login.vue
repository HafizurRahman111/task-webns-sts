<template>
    <SiteLayout>
        <!-- Login Section -->
        <section class="flex items-center justify-center min-h-screen bg-gray-100 p-4">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">Login</h2>

                <!-- Login Form -->
                <form @submit.prevent="handleLogin">
                    <!-- Email Input -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" v-model="email" placeholder="Enter your email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300"
                            :class="{ 'border-red-500': errors.email }" required />
                        <p v-if="errors.email" class="text-sm text-red-500 mt-1">{{ errors.email }}</p>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-6 relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" v-model="password"
                                placeholder="Enter your password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300"
                                :class="{ 'border-red-500': errors.password }" required />
                            <button type="button" @click="togglePasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                <span v-if="showPassword" class="text-gray-500">👁️</span>
                                <span v-else class="text-gray-500">👁️‍🗨️</span>
                            </button>
                        </div>
                        <p v-if="errors.password" class="text-sm text-red-500 mt-1">{{ errors.password }}</p>
                    </div>

                    <!-- Login Button -->
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                        Login
                    </button>
                </form>

                <!-- Sign Up Link -->
                <p class="mt-6 text-center text-gray-600">
                    Don't have an account?
                    <router-link to="/register" class="text-blue-600 hover:underline">Sign Up</router-link>
                </p>

                <!-- Error Message -->
                <p v-if="errorMessage" class="text-sm text-red-500 mt-4 text-center">
                    {{ errorMessage }}
                </p>
            </div>
        </section>
    </SiteLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import SiteLayout from '@/components/layouts/SiteLayout.vue';

// Refs for form inputs
const email = ref('');
const password = ref('');
const showPassword = ref(false);
const errorMessage = ref('');

// Error handling
const errors = ref({
    email: '',
    password: '',
});

// Router instance
const router = useRouter();

// Toggle password visibility
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

// Handle login
const handleLogin = async () => {
    errorMessage.value = ''; // Reset error message
    errors.value = { email: '', password: '' }; // Reset validation errors

    try {
        const response = await api.login({
            email: email.value,
            password: password.value,
        });

        // Save token to localStorage (or use a secure storage method)
        localStorage.setItem('token', response.data.token);

        // Redirect to dashboard after successful login
        router.push('/dashboard');
    } catch (error) {
        if (error.response && error.response.status === 401) {
            // Handle unauthorized error
            errorMessage.value = 'The provided credentials are incorrect.';
        } else if (error.response && error.response.data.errors) {
            // Handle Laravel validation errors
            const apiErrors = error.response.data.errors;
            errors.value = {
                email: apiErrors.email ? apiErrors.email[0] : '',
                password: apiErrors.password ? apiErrors.password[0] : '',
            };
        } else {
            console.error('Login failed:', error);
        }
    }
};
</script>

<style scoped>
/* No custom CSS needed - Tailwind handles everything */
</style>
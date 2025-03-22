<template>
    <SiteLayout>
        <!-- Register Section -->
        <section class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">Register</h2>

                <!-- Register Form -->
                <form @submit.prevent="handleRegister">
                    <!-- Name Input -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" v-model="name" placeholder="Enter your full name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300"
                            :class="{ 'border-red-500': errors.name }" required />
                        <p v-if="errors.name" class="text-sm text-red-500 mt-1">{{ errors.name }}</p>
                    </div>

                    <!-- Email Input -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" v-model="email" placeholder="Enter your email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300"
                            :class="{ 'border-red-500': errors.email }" required />
                        <p v-if="errors.email" class="text-sm text-red-500 mt-1">{{ errors.email }}</p>
                    </div>

                    <!-- Phone Input -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" v-model="phone" placeholder="Enter your phone number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300"
                            :class="{ 'border-red-500': errors.phone }" required />
                        <p v-if="errors.phone" class="text-sm text-red-500 mt-1">{{ errors.phone }}</p>
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

                    <!-- Confirm Password Input -->
                    <div class="mb-6 relative">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                            Password</label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword"
                                v-model="confirmPassword" placeholder="Confirm your password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300"
                                :class="{ 'border-red-500': errors.confirmPassword }" required />
                            <button type="button" @click="toggleConfirmPasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                <span v-if="showConfirmPassword" class="text-gray-500">👁️</span>
                                <span v-else class="text-gray-500">👁️‍🗨️</span>
                            </button>
                        </div>
                        <p v-if="errors.confirmPassword" class="text-sm text-red-500 mt-1">{{ errors.confirmPassword }}
                        </p>
                    </div>

                    <!-- Register Button -->
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                        Register
                    </button>
                </form>

                <!-- Login Link -->
                <p class="mt-6 text-center text-gray-600">
                    Already have an account?
                    <router-link to="/login" class="text-blue-600 hover:underline">Login</router-link>
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
const name = ref('');
const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);

// Error handling
const errors = ref({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
});

// Router instance
const router = useRouter();

// Toggle password visibility
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

// Toggle confirm password visibility
const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

// Form validation
const validateForm = () => {
    errors.value = { name: '', email: '', phone: '', password: '', confirmPassword: '' }; // Reset errors

    // Name validation
    if (!name.value) {
        errors.value.name = 'Name is required.';
    } else if (name.value.length < 4 || name.value.length > 255) {
        errors.value.name = 'Name must be between 4 and 255 characters.';
    }

    // Email validation
    if (!email.value) {
        errors.value.email = 'Email is required.';
    } else if (!/\S+@\S+\.\S+/.test(email.value)) {
        errors.value.email = 'Email is invalid.';
    } else if (email.value.length < 4 || email.value.length > 255) {
        errors.value.email = 'Email must be between 4 and 255 characters.';
    }

    // Phone validation
    if (!phone.value) {
        errors.value.phone = 'Phone number is required.';
    } else if (phone.value.length < 4 || phone.value.length > 20) {
        errors.value.phone = 'Phone number must be between 4 and 20 characters.';
    }

    // Password validation
    if (!password.value) {
        errors.value.password = 'Password is required.';
    } else if (password.value.length < 8 || password.value.length > 50) {
        errors.value.password = 'Password must be between 8 and 50 characters.';
    }

    // Confirm Password validation
    if (!confirmPassword.value) {
        errors.value.confirmPassword = 'Confirm Password is required.';
    } else if (confirmPassword.value !== password.value) {
        errors.value.confirmPassword = 'Passwords do not match.';
    }

    // Return true if no errors
    return (
        !errors.value.name &&
        !errors.value.email &&
        !errors.value.phone &&
        !errors.value.password &&
        !errors.value.confirmPassword
    );
};

// Handle register
const handleRegister = async () => {
    if (!validateForm()) return; // Stop if validation fails

    try {
        const response = await api.register({
            name: name.value,
            email: email.value,
            phone: phone.value,
            password: password.value,
            password_confirmation: confirmPassword.value,
        });

        console.log('Registration successful:', response.data);
        router.push('/login'); // Redirect to login page
    } catch (error) {
        if (error.response && error.response.data.errors) {
            // Handle Laravel validation errors
            const apiErrors = error.response.data.errors;
            errors.value = {
                name: apiErrors.name ? apiErrors.name[0] : '',
                email: apiErrors.email ? apiErrors.email[0] : '',
                phone: apiErrors.phone ? apiErrors.phone[0] : '',
                password: apiErrors.password ? apiErrors.password[0] : '',
                confirmPassword: apiErrors.password_confirmation ? apiErrors.password_confirmation[0] : '',
            };
        } else {
            console.error('Registration failed:', error);
        }
    }
};
</script>

<style scoped>
/* No custom CSS needed - Tailwind handles everything */
</style>
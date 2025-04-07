<template>
    <div class="ticket-management">
        <!-- Main Content -->
        <div class="p-4 max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
                    <h2 class="text-2xl font-bold text-gray-500">Edit Ticket</h2>
                    <p class="text-gray-400 mt-1">Update the form below to modify your support request</p>
                </div>

                <form @submit.prevent="submitForm" class="p-6" method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="subject" class="form-label">Subject *</label>
                                <input v-model="form.subject" type="text" id="subject" class="form-input"
                                    :class="{ 'form-input-error': errors.subject }"
                                    placeholder="Brief summary of your issue">
                                <div v-if="errors.subject" class="form-error">{{ errors.subject[0] }}</div>
                            </div>

                            <div class="form-group">
                                <label for="description" class="form-label">Description *</label>
                                <textarea v-model="form.description" id="description" rows="5" class="form-input"
                                    :class="{ 'form-input-error': errors.description }"
                                    placeholder="Detailed description of your issue"></textarea>
                                <div v-if="errors.description" class="form-error">{{ errors.description[0] }}</div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="form-group">
                                <label for="category" class="form-label">Category *</label>
                                <select v-model="form.category" id="category" class="form-input"
                                    :class="{ 'form-input-error': errors.category }">
                                    <option value="" disabled>Select a category</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="billing">Billing Inquiry</option>
                                    <option value="general">General Question</option>
                                </select>
                                <div v-if="errors.category" class="form-error">{{ errors.category[0] }}</div>
                            </div>

                            <div class="form-group">
                                <label for="priority" class="form-label">Priority *</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button type="button" @click="form.priority = 'low'"
                                        :class="priorityClass('low')">Low</button>
                                    <button type="button" @click="form.priority = 'medium'"
                                        :class="priorityClass('medium')">Medium</button>
                                    <button type="button" @click="form.priority = 'high'"
                                        :class="priorityClass('high')">High</button>
                                </div>
                                <div v-if="errors.priority" class="form-error">{{ errors.priority[0] }}</div>
                            </div>

                            <div class="form-group">
                                <label for="attachment" class="form-label">Attachment</label>
                                <div class="mt-1 flex items-center">
                                    <label for="attachment"
                                        class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50">
                                        Choose File
                                    </label>
                                    <span class="ml-2 text-sm text-gray-500">{{ form.attachment ? form.attachment.name :
                                        'No file chosen' }}</span>
                                    <input type="file" id="attachment" @change="handleFileUpload" class="hidden"
                                        accept=".jpg,.jpeg,.png" />
                                </div>
                                <div v-if="errors.attachment" class="form-error">{{ errors.attachment[0] }}</div>
                                <p class="mt-2 text-xs text-gray-500">Allowed: JPG, JPEG, PNG (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                        <button type="button" @click="cancelForm"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors flex items-center">
                            <span v-if="submitting" class="animate-spin h-4 w-4 mr-2">🔄</span>
                            <span>{{ submitting ? 'Updating Ticket...' : 'Update Ticket' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast Notification -->
        <div v-if="toast.show"
            class="fixed bottom-4 right-4 px-4 py-2 text-sm font-semibold text-white rounded-lg shadow-lg animate-fade-in"
            :class="toastClass">
            {{ toast.message }}
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/api.js'
import { useAuthStore } from '@/stores/auth.js'

// Use the auth store
const authStore = useAuthStore();
const router = useRouter();

// Reactive state
const form = ref({
    subject: '',
    description: '',
    category: '',
    priority: 'low',
    attachment: null
});
const errors = ref({});
const submitting = ref(false);
const toast = ref({ show: false, message: '' });

// Fetch ticket details
const fetchTicket = async () => {
    const ticketId = router.currentRoute.value.params.id;
    try {
        const response = await apiClient.getTicket(ticketId);
        const ticketData = response.data.data;

        // Populate form with ticket data
        form.value = {
            subject: ticketData.subject,
            description: ticketData.description,
            category: ticketData.category,
            priority: ticketData.priority,
            attachment: null // Reset attachment
        };
    } catch (err) {
        console.error('Failed to fetch ticket:', err);
        showToast('Failed to load ticket data', 'error');
    }
};

// Handle file upload
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            errors.value.attachment = ['File size must be less than 2MB'];
            form.value.attachment = null;
            event.target.value = '';
            return;
        }
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            errors.value.attachment = ['Only JPG, JPEG, and PNG files are allowed'];
            form.value.attachment = null;
            event.target.value = '';
            return;
        }
        form.value.attachment = file;
        errors.value.attachment = null;
    } else {
        form.value.attachment = null;
    }
};

// Submit form
const submitForm = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        Object.entries(form.value).forEach(([key, value]) => {
            formData.append(key, value);
        });
        formData.append('user_id', authStore.user.id);

        const response = await apiClient.updateTicket(formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.success) {
            showToast('Ticket updated successfully!', 'success');
            router.push({ name: 'Tickets' });
        } else {
            showToast(response.data.message || 'Unexpected response', 'warning');
        }
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
            showToast('Please fix the form errors', 'error');
        } else {
            showToast(err.response?.data.message || 'Failed to update ticket', 'error');
        }
    } finally {
        submitting.value = false;
    }
};

// Cancel form
const cancelForm = () => {
    router.push({ name: 'tickets' });
}

// Show toast notification
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 3000);
}

// Priority class helper
const priorityClass = (priority) => {
    return {
        'priority-option': true,
        'priority-option-selected': form.value.priority === priority,
        'priority-option-low': priority === 'low',
        'priority-option-medium': priority === 'medium',
        'priority-option-high': priority === 'high',
    };
};

// Initialize on mount
onMounted(() => {
    fetchTicket();
});
</script>

<style scoped>
.ticket-management {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.3rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #374151;
}

.form-input {
    display: block;
    width: 100%;
    padding: 0.3rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: white;
    transition: border-color 0.15s ease-in-out;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input-error {
    border-color: #ef4444;
}

.form-error {
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #ef4444;
}

/* Priority Options */
.priority-option {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.priority-option:hover {
    background-color: #f3f4f6;
}

.priority-option-selected {
    border-color: transparent;
    color: white;
}

.priority-option-low.priority-option-selected {
    background-color: #10b981;
}

.priority-option-medium.priority-option-selected {
    background-color: #f59e0b;
}

.priority-option-high.priority-option-selected {
    background-color: #ef4444;
}

/* Animations */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<template>
  <div class="ticket-management">

    <!-- Main Content -->
    <div class="p-4 max-w-6xl mx-auto">
      <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
          <h2 class="text-2xl font-bold text-gray-500">Create New Ticket</h2>
          <p class="text-gray-400 mt-1">Fill out the form below to submit a new support request</p>
        </div>

        <form @submit.prevent="submitForm" class="p-6" method="POST" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
              <!-- Subject Field -->
              <div class="form-group">
                <label for="subject" class="form-label">Subject *</label>
                <input v-model="form.subject" type="text" id="subject" class="form-input"
                  :class="{ 'form-input-error': errors.subject }" placeholder="Brief summary of your issue">
                <div v-if="errors.subject" class="form-error">
                  {{ errors.subject[0] }}
                </div>
              </div>

              <!-- Description Field -->
              <div class="form-group">
                <label for="description" class="form-label">Description *</label>
                <textarea v-model="form.description" id="description" rows="5" class="form-input"
                  :class="{ 'form-input-error': errors.description }"
                  placeholder="Detailed description of your issue"></textarea>
                <div v-if="errors.description" class="form-error">
                  {{ errors.description[0] }}
                </div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
              <!-- Category Field -->
              <div class="form-group">
                <label for="category" class="form-label">Category *</label>
                <select v-model="form.category" id="category" class="form-input"
                  :class="{ 'form-input-error': errors.category }">
                  <option value="" disabled>Select a category</option>
                  <option value="technical">Technical Support</option>
                  <option value="billing">Billing Inquiry</option>
                  <option value="general">General Question</option>
                </select>
                <div v-if="errors.category" class="form-error">
                  {{ errors.category[0] }}
                </div>
              </div>

              <!-- Priority Field -->
              <div class="form-group">
                <label for="priority" class="form-label">Priority *</label>
                <div class="grid grid-cols-3 gap-3">
                  <button type="button" @click="form.priority = 'low'" :class="{
                    'priority-option': true,
                    'priority-option-selected': form.priority === 'low',
                    'priority-option-low': true
                  }">
                    Low
                  </button>
                  <button type="button" @click="form.priority = 'medium'" :class="{
                    'priority-option': true,
                    'priority-option-selected': form.priority === 'medium',
                    'priority-option-medium': true
                  }">
                    Medium
                  </button>
                  <button type="button" @click="form.priority = 'high'" :class="{
                    'priority-option': true,
                    'priority-option-selected': form.priority === 'high',
                    'priority-option-high': true
                  }">
                    High
                  </button>
                </div>
                <div v-if="errors.priority" class="form-error">
                  {{ errors.priority[0] }}
                </div>
              </div>

              <div class="form-group">
                <label for="attachment" class="form-label">Attachment</label>
                <div class="mt-1 flex items-center">
                  <label for="attachment"
                    class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Choose File
                  </label>
                  <span class="ml-2 text-sm text-gray-500" v-if="form.attachment">
                    {{ form.attachment.name }}
                  </span>
                  <span class="ml-2 text-sm text-gray-500" v-else>
                    No file chosen
                  </span>
                  <input type="file" id="attachment" @change="handleFileUpload" class="hidden"
                    accept=".jpg,.jpeg,.png" />
                </div>
                <div v-if="errors.attachment" class="form-error">
                  {{ errors.attachment[0] }}
                </div>
                <p class="mt-2 text-xs text-gray-500">Allowed: JPG, JPEG, PNG (Max 2MB)</p>
              </div>

            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
            <button type="button" @click="cancelForm"
              class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
              Cancel
            </button>
            <button type="submit" :disabled="submitting"
              class="px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center">
              <span v-if="submitting" class="animate-spin h-4 w-4 mr-2">🔄</span>
              <span>{{ submitting ? 'Creating Ticket...' : 'Create Ticket' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="toast.show"
      class="fixed bottom-4 right-4 px-4 py-2 text-sm font-semibold text-white rounded-lg shadow-lg animate-fade-in"
      :class="{
        'bg-green-600': toast.type === 'success',
        'bg-red-600': toast.type === 'error'
      }">
      {{ toast.message }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import apiClient from '@/services/api.js';

const router = useRouter()
const authStore = useAuthStore()

// Form state
const form = ref({
  subject: '',
  description: '',
  category: '',
  priority: 'low',
  attachment: null
})

// UI state
const loading = ref(false)
const error = ref(null)
const submitting = ref(false)
const errors = ref({})
const toast = ref({ show: false, message: '', type: 'success' })

// Initialize form
const initForm = () => {
  loading.value = false
  error.value = null
  form.value = {
    subject: '',
    description: '',
    category: '',
    priority: 'low',
    attachment: null
  }
  errors.value = {}
}

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      errors.value.attachment = ['File size must be less than 2MB'];
      form.value.attachment = null; // Clear the attachment
      event.target.value = ''; // Reset the file input
      console.log(`File selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
      return;
    }

    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!validTypes.includes(file.type)) {
      errors.value.attachment = ['Only JPG, JPEG, and PNG files are allowed'];
      form.value.attachment = null; // Clear the attachment
      event.target.value = ''; // Reset the file input
      return;
    }

    // If validation passes
    form.value.attachment = file;
    errors.value.attachment = null;
  } else {
    form.value.attachment = null;
  }
};

// Submit form
const submitForm = async () => {
  submitting.value = true; // Indicate that the form is being submitted
  errors.value = {}; // Reset errors

  try {
    const formData = new FormData();
    formData.append('subject', form.value.subject);
    formData.append('description', form.value.description);
    formData.append('category', form.value.category);
    formData.append('priority', form.value.priority);
    formData.append('user_id', authStore.user.id); // Include user ID

    if (form.value.attachment) {
      formData.append('attachment', form.value.attachment);
    }

    // Make the API call to create the ticket
    const response = await apiClient.createTicket(formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    console.log('API Response:', response.data); // Log the entire response for debugging

    // Handle successful response
    if (response.data.success) { // Check if success is truthy
      showToast('Ticket created successfully!', 'success');
      router.push({ name: 'Tickets' }); // Redirect to the tickets page
    } else {
      // Handle unexpected response format
      showToast(response.data.message || 'Ticket created but with unexpected response', 'warning');
      router.push({ name: 'Tickets' }); // Redirect to the tickets page
    }
  } catch (err) {
    console.error('Error:', err); // Log the error for debugging

    // Handle validation errors
    if (err.response?.status === 422) {
      // Validation errors
      errors.value = err.response.data.errors || {};
      showToast('Please fix the form errors', 'error');
    } else if (err.response) {
      // Handle other errors with a response
      error.value = {
        message: err.response.data.message || 'Failed to create ticket'
      };
      showToast(error.value.message, 'error');
    } else {
      // Handle network or other errors without a response
      showToast('An unexpected error occurred. Please try again.', 'error');
    }
  } finally {
    submitting.value = false; // Reset submitting state
  }
};

// Cancel form
const cancelForm = () => {
  router.push({ name: 'tickets' })
}

// Show toast notification
const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 3000)
}

// Initialize on mount
onMounted(initForm)
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

.form-input-error:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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
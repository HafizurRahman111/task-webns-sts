<template>

    <div class="p-1">
        <!-- Back Button -->
        <button @click="goBack"
            class="mb-4 px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Tickets
        </button>

        <!-- Ticket Details Section -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-2">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Ticket Details</h2>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center h-32">
                <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="flex justify-center items-center h-32">
                <span class="text-red-600">Failed to load ticket details. Please try again.</span>
            </div>

            <!-- Ticket Details -->
            <div v-else class="space-y-4">
                <!-- Ticket Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left Column -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-base font-bold text-gray-600 mt-1">Subject</label>
                            <p class="text-sm text-gray-800">{{ ticket.subject }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-base font-bold text-gray-600 mt-1">Description</label>
                            <p class="text-sm text-gray-800">{{ ticket.description }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-base font-bold text-gray-600 mt-1">Category</label>
                            <p class="text-sm text-gray-800">{{ ticket.category }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <div class="mb-4">
                            <label class="block text-base font-bold text-gray-600 mt-1">Priority</label>
                            <span :class="getPriorityClass(ticket.priority)"
                                class="px-3 py-1 text-sm font-semibold rounded-full">
                                {{ ticket.priority }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="block text-base font-bold text-gray-600 mt-1">Status</label>
                            <span :class="getStatusClass(ticket.status)"
                                class="px-3 py-1 text-sm font-semibold rounded-full">
                                {{ ticket.status }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="block text-base font-bold text-gray-600 mt-1">Created At</label>
                            <p class="text-sm text-gray-800">{{ formattedDate }}</p>
                        </div>

                        <div v-if="ticket.attachment" class="mt-4">
                            <h3 class="text-lg font-medium text-gray-900">Attachment</h3>
                            <div class="mt-2 flex items-center">
                                <img v-if="isImage(ticket.attachment)" :src="ticket.attachment" alt="Ticket Attachment"
                                    class="max-w-full h-auto rounded" />
                                <span v-else class="ml-2 text-sm text-gray-500">
                                    {{ ticket.attachment }}
                                </span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="mt-2 bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Comments</h2>

            <!-- Add Comment Form -->
            <div class="mb-2 flex items-center space-x-2">
                <textarea v-model="newComment" placeholder="Add a comment..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <button @click="addComment"
                    class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Add Comment
                </button>
            </div>
            <!-- Error Message -->
            <div v-if="commentError" class="text-red-600 text-sm mt-1">{{ commentError }}</div>

            <!-- Comments List -->
            <div v-if="ticket.comments && ticket.comments.length > 0">
                <div v-for="comment in ticket.comments" :key="comment.id" class="mb-4">
                    <div class="flex items-start space-x-4 mt-2">
                        <!-- User Avatar -->
                        <div class="flex-shrink-0 m-2">
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-600">{{ comment.user?.name?.charAt(0) || 'U' }}</span>
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="flex-1">
                            <div :class="getCommentUserClass(comment.user_id)" class="text-sm font-semibold">
                                {{ comment.user?.name || 'Unknown User' }}
                            </div>
                            <div :class="getCommentContentClass(comment.user_id)" class="text-sm">
                                {{ comment.content }}
                            </div>
                            <div class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</div>
                        </div>

                        <!-- Delete Button (Only for authenticated user's comments) -->
                        <div v-if="comment.user_id === currentUserId" class="flex-shrink-0">
                            <button @click="confirmDeleteComment(comment)" class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-gray-500">No comments yet.</div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="toast.show"
        class="fixed bottom-4 right-4 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow-lg animate-fade-in">
        {{ toast.message }}
    </div>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import apiClient from '@/services/api.js';
import { useAuthStore } from '@/stores/auth.js';

// Use the auth store
const authStore = useAuthStore();

const router = useRouter();

const isImage = (url) => {
    // Check if the URL ends with an image extension
    return /\.(jpeg|jpg|png|gif|bmp|webp)$/i.test(url);
};

// Reactive state
const ticket = ref({
    id: null,
    subject: '',
    description: '',
    category: '',
    priority: '',
    status: '',
    attachment: '',
    user: { name: '' },
    created_at: '',
    comments: [],
});
const loading = ref(true);
const error = ref(false);
const newComment = ref('');
const commentError = ref('');
const toast = ref({ show: false, message: '' });

// Reactive state additions
const attachmentUrl = ref('');
const attachmentLoading = ref(false);

// Fetch the current authenticated user ID
const currentUserId = computed(() => authStore.user.data?.id || null);

// Fetch ticket details
const fetchTicket = async () => {
    try {
        loading.value = true;
        error.value = false;
        const ticketId = router.currentRoute.value.params.id;
        const response = await apiClient.getTicket(ticketId);
        ticket.value = response.data.data;

    } catch (err) {
        console.error('Failed to fetch ticket:', err);
        error.value = true;
    } finally {
        loading.value = false;
    }
};


// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Computed property for formatted date
const formattedDate = computed(() => formatDate(ticket.value.created_at));

// Get priority class based on value
const getPriorityClass = (priority) => {
    switch (priority) {
        case 'low':
            return 'bg-green-100 text-green-600';
        case 'medium':
            return 'bg-yellow-100 text-yellow-600';
        case 'high':
            return 'bg-red-100 text-red-600';
        default:
            return 'bg-gray-100 text-gray-600';
    }
};

// Get status class based on value
const getStatusClass = (status) => {
    switch (status) {
        case 'open':
            return 'bg-blue-100 text-blue-600';
        case 'in-progress':
            return 'bg-yellow-100 text-yellow-600';
        case 'resolved':
            return 'bg-green-100 text-green-600';
        case 'closed':
            return 'bg-gray-100 text-gray-600';
        default:
            return 'bg-gray-100 text-gray-600';
    }
};

// Get comment user class based on user ID
const getCommentUserClass = (userId) => {
    return userId === currentUserId.value ? 'text-blue-600' : 'text-green-600';
};

// Get comment content class based on user ID
const getCommentContentClass = (userId) => {
    return userId === currentUserId.value ? 'text-blue-800' : 'text-green-800';
};

// Add a comment
const addComment = async () => {
    if (!newComment.value.trim()) {
        commentError.value = 'Comment cannot be empty.';
        return;
    }

    try {
        const response = await apiClient.addComment(ticket.value.id, { content: newComment.value });
        ticket.value.comments.push(response.data.data);
        newComment.value = '';
        commentError.value = '';
        showToast('Comment added successfully!');
    } catch (err) {
        console.error('Failed to add comment:', err);
        showToast('Failed to add comment. Please try again.', 'error');
    }
};

// Confirm delete comment using SweetAlert2
const confirmDeleteComment = async (comment) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this comment!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    });

    if (result.isConfirmed) {
        await deleteComment(comment);
    }
};

// Delete a comment
const deleteComment = async (comment) => {
    try {
        await apiClient.deleteComment(ticket.value.id, comment.id);
        ticket.value.comments = ticket.value.comments.filter(c => c.id !== comment.id);
        showToast('Comment deleted successfully!');
    } catch (err) {
        console.error('Failed to delete comment:', err);
        showToast('Failed to delete comment. Please try again.', 'error');
    }
};

// Show toast notification
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Go back to tickets list
const goBack = () => {
    router.push({ name: 'Tickets' });
};

// Fetch ticket on mount
onMounted(() => {
    fetchTicket();
});
</script>

<style scoped>
.mb-1 {
    margin-bottom: .25rem;
}

.mb-2 {
    margin-bottom: .5rem;
}

.mt-1 {
    margin-top: .25rem;
}

.mt-2 {
    margin-bottom: .5rem;
}

.mr-1 {
    margin-bottom: .25rem;
}

.mr-2 {
    margin-bottom: .5rem;
}

.m-2 {
    margin-top: .5rem;
    margin-left: .5rem;
    margin-right: .5rem;
    margin-bottom: .5rem;
}

.font-bold {
    font-weight: 500;
}
</style>
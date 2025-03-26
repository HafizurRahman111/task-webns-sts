<template>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Support Chats</h1>

        <!-- Main Layout -->
        <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-180px)]">
            <!-- Chat List Sidebar -->
            <div class="w-full lg:w-1/3 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Recent Chats</h2>
                    <p class="text-sm text-gray-500 mt-1">Select a conversation to view messages</p>
                </div>

                <!-- Loading/Error States -->
                <div v-if="loading" class="flex justify-center items-center p-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div v-else-if="error" class="p-8 text-center">
                    <div class="text-red-500 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Failed to load conversations</span>
                        <button @click="fetchLatestChats"
                            class="mt-2 px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200">
                            Retry
                        </button>
                    </div>
                </div>

                <!-- Chat List -->
                <!-- Chat List -->
                <div v-else class="divide-y divide-gray-200 overflow-y-auto max-h-[calc(100vh-250px)]">
                    <div v-for="chat in recentChats" :key="chat.ticket.id" @click="selectTicket(chat.ticket.id)" :class="[
                        'p-4 hover:bg-gray-50 cursor-pointer transition-colors relative',
                        selectedTicketId === chat.ticket.id ? 'bg-blue-50 border-l-4 border-blue-500' : '',
                        chat.unread_count > 0 ? 'bg-gray-50' : ''
                    ]">
                        <!-- Unread indicator -->
                        <div v-if="chat.unread_count > 0" class="absolute top-2 right-2">
                            <span
                                class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-xs text-white">
                                {{ chat.unread_count }}
                            </span>
                        </div>

                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-800 flex items-center">
                                    #{{ chat.ticket.id }} - {{ chat.ticket.subject }}
                                    <span v-if="chat.unread_count > 0"
                                        class="ml-2 h-2 w-2 rounded-full bg-red-500"></span>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-1">
                                    <span :class="[
                                        'font-medium',
                                        chat.unread_count > 0 ? 'text-gray-900' : 'text-gray-600'
                                    ]">
                                        {{ chat.last_message?.user?.name || 'Unknown User' }}:
                                    </span>
                                    <span :class="chat.unread_count > 0 ? 'font-bold' : ''">
                                        {{ chat.last_message?.message || 'No messages yet.' }}
                                    </span>
                                </p>
                            </div>
                            <span :class="[
                                'px-2 py-1 rounded-full text-xs font-medium',
                                getPriorityClass(chat.ticket.priority)
                            ]">
                                {{ chat.ticket.priority }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs"
                                :class="chat.unread_count > 0 ? 'text-gray-800 font-medium' : 'text-gray-500'">
                                {{ formatTimeAgo(chat.last_message?.created_at) || 'N/A' }}
                            </span>
                            <span :class="[
                                'px-2 py-1 rounded-full text-xs',
                                getStatusClass(chat.ticket.status)
                            ]">
                                {{ chat.ticket.status }}
                            </span>
                        </div>
                    </div> <!-- Closing div for chat item -->
                </div> <!-- Closing div for chat list -->


            </div>

            <!-- Chat Panel -->
            <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Chat Header -->
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ selectedTicket ? `Ticket #${selectedTicket.id}` : 'Select a conversation' }}
                        </h2>
                        <p v-if="selectedTicket" class="text-sm text-gray-500">
                            {{ selectedTicket.subject }}
                            <span class="ml-2" :class="getPriorityClass(selectedTicket.priority)">
                                {{ selectedTicket.priority }}
                            </span>
                        </p>
                    </div>
                    <div v-if="selectedTicket" class="flex space-x-2">
                        <button @click="refreshChats" class="p-2 text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Chat Loading/Empty States -->
                <div v-if="!selectedTicketId" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No conversation selected</h3>
                    <p class="text-gray-500 max-w-md">Select a conversation from the list to view messages</p>
                </div>
                <div v-else-if="loadingChat" class="flex-1 flex items-center justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div v-else-if="errorChat" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-400 mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Failed to load messages</h3>
                    <button @click="fetchTicketChats" class="mt-4 px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200">
                        Retry
                    </button>
                </div>

                <!-- Chat Messages -->
                <div v-else class="flex-1 overflow-y-auto p-4 bg-gray-50" ref="chatContainer" @scroll="handleScroll">
                    <!-- Load More Indicator -->
                    <div v-if="loadingOlderChats" class="flex justify-center py-4">
                        <div class="animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-blue-500"></div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!loadingOlderChats && selectedTicketChats.length === 0"
                        class="flex flex-col items-center justify-center h-full text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-700 mb-1">No messages yet</h3>
                        <p class="text-gray-500">Be the first to start the conversation</p>
                    </div>

                    <!-- Messages -->
                    <!-- Messages -->
                    <div v-for="chat in selectedTicketChats" :key="chat.id"
                        :class="['flex mb-4', chat.user_id === currentUserId ? 'justify-end' : 'justify-start']">
                        <div
                            :class="['flex max-w-[80%]', chat.user_id === currentUserId ? 'flex-row-reverse' : 'flex-row']">
                            <!-- Avatar -->
                            <div
                                class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-700 font-medium text-sm">
                                    {{ chat.user?.name?.charAt(0) || 'U' }}
                                </span>
                            </div>

                            <!-- Message Content -->
                            <div :class="['ml-3 mr-3', chat.user_id === currentUserId ? 'text-right' : 'text-left']">
                                <div class="text-sm font-medium text-gray-700 mb-1">
                                    {{ chat.user_id === currentUserId ? 'You' : chat.user?.name || 'Unknown User' }}
                                </div>
                                <div :class="[
                                    'p-3 rounded-lg inline-block',
                                    chat.user_id === currentUserId
                                        ? 'bg-blue-500 text-white' // Current user's message
                                        : 'bg-white border border-gray-200 text-gray-800', // Other users' messages
                                    chat.status === 'unread' ? 'ring-2 ring-blue-300' : '' // Highlight unread messages
                                ]">
                                    {{ chat.message }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ formatTime(chat.created_at) }}
                                    <span v-if="chat.status === 'unread' && chat.user_id !== currentUserId"
                                        class="ml-2 text-blue-500">• New</span>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Message Input -->
                <div v-if="selectedTicketId" class="p-4 border-t border-gray-200 bg-white">
                    <form @submit.prevent="sendMessage" class="flex items-end gap-2">
                        <div class="flex-1">
                            <textarea v-model="newMessage" @keydown.enter.exact.prevent="sendMessage"
                                @keydown.shift.enter="newMessage += '\n'" placeholder="Type your message..." rows="2"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                :disabled="sendingMessage"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                Press Enter to send, Shift+Enter for new line
                            </div>
                        </div>
                        <button type="submit" :disabled="!newMessage.trim() || sendingMessage" :class="[
                            'px-4 py-2 rounded-lg transition-colors',
                            newMessage.trim() && !sendingMessage
                                ? 'bg-blue-600 text-white hover:bg-blue-700'
                                : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                        ]">
                            <span v-if="!sendingMessage">Send</span>
                            <span v-else class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <transition enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="toast.show" :class="[
                'fixed bottom-4 right-4 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden',
                toast.type === 'success' ? 'bg-green-600' :
                    toast.type === 'error' ? 'bg-red-600' : 'bg-blue-600'
            ]">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg v-if="toast.type === 'success'" class="h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg v-else-if="toast.type === 'error'" class="h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-white">{{ toast.message }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="toast.show = false" class="inline-flex text-white focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, onUnmounted, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/services/api';
import pusher from '@/services/pusher';

const authStore = useAuthStore();

// State
const recentChats = ref([]);
const selectedTicketId = ref(null);
const selectedTicket = ref(null);
const selectedTicketChats = ref([]);
const loading = ref(true);
const error = ref(false);
const loadingChat = ref(false);
const errorChat = ref(false);
const loadingOlderChats = ref(false);
const newMessage = ref('');
const toast = ref({ show: false, message: '', type: 'success' });
const sendingMessage = ref(false);
const chatContainer = ref(null);
const currentPage = ref(1);
const hasMoreChats = ref(true);
const totalChats = ref(0);

onMounted(async () => {
    await authStore.fetchUser();
    console.log('Current User ID:', currentUserId.value);
});

const currentUserId = computed(() => {
    return authStore.user.id || null;
});

// Pusher channel reference
let pusherChannel = null;
let tempMessage = null; // Moved tempMessage outside to avoid scope issues

// Methods
const fetchLatestChats = async () => {
    try {
        loading.value = true;
        error.value = false;

        const response = await apiClient.getAllChats();

        // console.log(response.data.data);
        const chatsData = response.data.data;

        if (Array.isArray(chatsData)) {
            recentChats.value = chatsData.sort((a, b) => {
                const lastMessageA = a.last_message ? new Date(a.last_message.created_at) : new Date(0);
                const lastMessageB = b.last_message ? new Date(b.last_message.created_at) : new Date(0);
                return lastMessageB - lastMessageA;
            });
        } else {
            recentChats.value = [];
        }
    } catch (err) {
        console.error('Failed to fetch chats:', err);
        error.value = true;
        showToast('Failed to load conversations. Please try again.', 'error');
    } finally {
        loading.value = false;
    }
};

const fetchTicketChats = async (page = 1) => {
    if (!selectedTicketId.value) return;

    try {
        loadingChat.value = page === 1;
        loadingOlderChats.value = page > 1;
        errorChat.value = false;

        const response = await apiClient.getChats(selectedTicketId.value, page);
        const newChats = response.data.data.data;

        console.log(newChats);

        if (page === 1) {
            // For the first page, set the chats and ticket info
            selectedTicketChats.value = newChats.reverse();
            selectedTicket.value = response.data.data.data.ticket;
            totalChats.value = response.data.data.data.total;

            // Mark as read for any unread chats when fetching the first page
            if (newChats.some(chat => chat.status === 'unread')) {
                await markAllChatsAsRead(selectedTicketId.value);
            }

            scrollToBottom(); // Scroll to the bottom after loading the first page
        } else {
            // For subsequent pages, prepend new chats to the existing list
            selectedTicketChats.value = [...newChats, ...selectedTicketChats.value];
        }

        hasMoreChats.value = newChats.length > 0 && selectedTicketChats.value.length < totalChats.value;
    } catch (err) {
        console.error('Failed to fetch chats:', err);
        errorChat.value = true;
        showToast('Failed to load messages. Please try again.', 'error');
    } finally {
        loadingChat.value = false;
        loadingOlderChats.value = false;
    }
};

// Function to mark a single chat as read
const markAllChatsAsRead = async (ticketId) => {
    try {
        await apiClient.markAllAsReadTicket(ticketId);

        // Update the local state to reflect that all chats are now read
        selectedTicketChats.value.forEach(chat => {
            chat.status = 'read'; // Mark each chat as read
        });

        console.log(selectedTicketChats.value);

        const recentChatIndex = recentChats.value.findIndex(c => c.ticket.id === ticketId);
        if (recentChatIndex !== -1) {
            recentChats.value[recentChatIndex].unread_count = 0;
        }
        showToast('All chats marked as read', 'success');
    } catch (err) {
        console.error('Failed to mark all chats as read:', err);
        showToast('Failed to mark all chats as read. Please try again.', 'error');
    }
};


const selectTicket = async (ticketId) => {
    if (pusherChannel) {
        pusher.unsubscribe(pusherChannel.name);
    }

    selectedTicketId.value = ticketId;
    selectedTicketChats.value = [];
    currentPage.value = 1;

    await fetchTicketChats();

    pusherChannel = pusher.subscribe(`private-ticket.${ticketId}`);
    pusherChannel.bind('NewChatMessage', (data) => {
        const isCurrentUser = data.user.id === currentUserId.value;

        if (!isCurrentUser) {
            showToast('New message received', 'info');
        }

        selectedTicketChats.value.push({
            ...data,
            status: isCurrentUser ? 'read' : 'unread'
        });

        const chatIndex = recentChats.value.findIndex(c => c.ticket.id === ticketId);
        if (chatIndex !== -1) {
            recentChats.value[chatIndex].last_message = data;
            if (!isCurrentUser) {
                recentChats.value[chatIndex].unread_count += 1;
            }

            if (chatIndex > 0) {
                const [chat] = recentChats.value.splice(chatIndex, 1);
                recentChats.value.unshift(chat);
            }
        }

        scrollToBottom();
    });
};

const refreshChats = async () => {
    await fetchTicketChats();
    await fetchLatestChats();
    showToast('Chat refreshed', 'success');
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    if (sendingMessage.value) return;

    try {
        sendingMessage.value = true;

        tempMessage = {
            id: Date.now(),
            message: newMessage.value,
            user: {
                id: currentUserId.value,
                name: authStore.user?.data?.name || 'You'
            },
            created_at: new Date().toISOString(),
            status: 'unread'
        };

        selectedTicketChats.value.push(tempMessage);
        newMessage.value = '';
        scrollToBottom();

        const response = await apiClient.sendChatMessage(selectedTicketId.value, tempMessage.message);

        const index = selectedTicketChats.value.findIndex(m => m.id === tempMessage.id);
        if (index !== -1) {
            selectedTicketChats.value.splice(index, 1, response.data.data);
        }

        const chatIndex = recentChats.value.findIndex(c => c.ticket.id === selectedTicketId.value);

        if (chatIndex !== -1) {
            recentChats.value[chatIndex].last_message = response.data.data;

            if (chatIndex > 0) {
                const [chat] = recentChats.value.splice(chatIndex, 1);
                recentChats.value.unshift(chat);
            }
        }

        showToast('Message sent', 'success');
    } catch (err) {
        console.error('Failed to send message:', err);

        if (tempMessage) {
            selectedTicketChats.value = selectedTicketChats.value.filter(m => m.id !== tempMessage.id);
        }

        showToast('Failed to send message', 'error');
    } finally {
        sendingMessage.value = false;
        tempMessage = null;
    }
};

const handleScroll = (event) => {
    if (!event.target || loadingOlderChats.value || !hasMoreChats.value) return;

    const { scrollTop } = event.target;
    const atTop = scrollTop === 0;

    if (atTop) {
        currentPage.value += 1;
        fetchTicketChats(currentPage.value);
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatTimeAgo = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    return `${Math.floor(diffInSeconds / 86400)}d ago`;
};

const getPriorityClass = (priority) => {
    const classes = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-red-100 text-red-800'
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
};

const getStatusClass = (status) => {
    const classes = {
        open: 'bg-blue-100 text-blue-800',
        'in-progress': 'bg-yellow-100 text-yellow-800',
        resolved: 'bg-green-100 text-green-800',
        closed: 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 5000);
};

// Lifecycle
onMounted(() => {
    fetchLatestChats();

});

onUnmounted(() => {
    if (pusherChannel) {
        pusher.unsubscribe(pusherChannel.name);
    }
});

// Watch for changes in selected ticket
watch(selectedTicketId, (newVal) => {
    if (newVal) {
        markAllChatsAsRead();
    }
});
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for new messages */
@keyframes highlight {
    0% {
        background-color: rgba(59, 130, 246, 0.1);
    }

    100% {
        background-color: transparent;
    }
}

.highlight-message {
    animation: highlight 2s ease-out;
}
</style>
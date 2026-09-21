<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    conversations: Array,
});

const page = usePage();
const authUser = page.props.auth.user;

const activeConversation = ref(props.conversations && props.conversations.length > 0 ? props.conversations[0] : null);
const messages = ref([]);
const newMessage = ref('');
const messagesContainer = ref(null);

const selectConversation = async (conv) => {
    activeConversation.value = conv;
    conv.unread_count = 0;
    fetchMessages();
};

const fetchMessages = async () => {
    if (!activeConversation.value) return;
    try {
        const res = await axios.get(route('chat.messages', activeConversation.value.user.id));
        messages.value = res.data;
        scrollToBottom();
    } catch (e) {
        console.error('Failed to load messages', e);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || !activeConversation.value) return;

    const msgText = newMessage.value;
    newMessage.value = '';

    try {
        const res = await axios.post(route('chat.send'), {
            receiver_id: activeConversation.value.user.id,
            message: msgText,
        });
        messages.value.push(res.data);
        scrollToBottom();
    } catch (e) {
        console.error('Failed to send message', e);
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

let echoChannel = null;

onMounted(() => {
    if (activeConversation.value) {
        fetchMessages();
    }

    if (window.Echo && authUser) {
        echoChannel = window.Echo.private(`chat.${authUser.id}`)
            .listen('.message.sent', (e) => {
                if (activeConversation.value && e.sender_id === activeConversation.value.user.id) {
                    messages.value.push({
                        id: e.id,
                        sender_id: e.sender_id,
                        receiver_id: e.receiver_id,
                        message: e.message,
                        created_at: e.created_at,
                    });
                    scrollToBottom();
                } else {
                    const conv = props.conversations.find(c => c.user.id === e.sender_id);
                    if (conv) {
                        conv.unread_count = (conv.unread_count || 0) + 1;
                        conv.last_message = e.message;
                    }
                }
            });
    }
});

onUnmounted(() => {
    if (echoChannel) {
        echoChannel.stopListening('.message.sent');
    }
});
</script>

<template>
    <Head title="Buyer Messages - Vendor Inbox" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Customer Messages &amp; Live Inquiries
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex h-[650px]">
                    
                    <!-- Sidebar Conversations List -->
                    <div class="w-1/3 border-r border-gray-100 bg-slate-50/50 flex flex-col">
                        <div class="p-4 border-b border-gray-100 font-bold text-gray-700 text-sm uppercase tracking-wider">
                            Conversations
                        </div>
                        <div class="overflow-y-auto flex-1 divide-y divide-gray-100">
                            <div
                                v-for="conv in conversations"
                                :key="conv.user.id"
                                @click="selectConversation(conv)"
                                class="p-4 hover:bg-white cursor-pointer transition flex items-center justify-between"
                                :class="activeConversation?.user.id === conv.user.id ? 'bg-white border-l-4 border-indigo-600 shadow-sm' : ''"
                            >
                                <div class="flex items-center space-x-3 truncate">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center flex-shrink-0">
                                        {{ conv.user.name.charAt(0) }}
                                    </div>
                                    <div class="truncate">
                                        <h4 class="font-bold text-gray-900 text-sm truncate">{{ conv.user.name }}</h4>
                                        <p class="text-xs text-gray-500 truncate">{{ conv.last_message }}</p>
                                    </div>
                                </div>
                                <span v-if="conv.unread_count > 0" class="w-5 h-5 bg-indigo-600 text-white font-bold text-[10px] rounded-full flex items-center justify-center flex-shrink-0">
                                    {{ conv.unread_count }}
                                </span>
                            </div>

                            <div v-if="!conversations || conversations.length === 0" class="p-8 text-center text-gray-400 text-sm">
                                No buyer inquiries yet.
                            </div>
                        </div>
                    </div>

                    <!-- Chat Message Thread -->
                    <div class="w-2/3 flex flex-col bg-white">
                        <template v-if="activeConversation">
                            <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/30">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm">
                                        {{ activeConversation.user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-sm">{{ activeConversation.user.name }}</h3>
                                        <p class="text-xs text-gray-400">{{ activeConversation.user.email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div ref="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/20">
                                <div
                                    v-for="msg in messages"
                                    :key="msg.id"
                                    class="flex"
                                    :class="msg.sender_id === authUser.id ? 'justify-end' : 'justify-start'"
                                >
                                    <div
                                        class="max-w-md px-4 py-3 rounded-2xl text-sm shadow-sm"
                                        :class="msg.sender_id === authUser.id ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none'"
                                    >
                                        <p>{{ msg.message }}</p>
                                        <div class="text-[10px] opacity-70 text-right mt-1">
                                            {{ new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Form -->
                            <form @submit.prevent="sendMessage" class="p-4 border-t border-gray-100 flex items-center space-x-3 bg-white">
                                <input
                                    v-model="newMessage"
                                    type="text"
                                    placeholder="Type your message..."
                                    class="flex-1 rounded-xl border-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <button
                                    type="submit"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition shadow-sm"
                                >
                                    Send
                                </button>
                            </form>
                        </template>

                        <div v-else class="flex-1 flex items-center justify-center text-gray-400 text-sm">
                            Select a conversation to start chatting.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    orders: Object,
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

// Live order status updates via Reverb
const liveStatuses = ref({});
const toastMessage = ref(null);
const toastTimeout = ref(null);
let echoChannel = null;

const STATUS_STEPS = ['paid', 'accepted', 'processing', 'shipped', 'out_for_delivery', 'delivered'];

function stepIndex(status) {
    const idx = STATUS_STEPS.indexOf(status);
    return idx === -1 ? 0 : idx;
}

function stepLabel(step) {
    const labels = {
        paid: 'Paid',
        accepted: 'Accepted',
        processing: 'Processing',
        shipped: 'Shipped',
        out_for_delivery: 'Out for Delivery',
        delivered: 'Delivered',
    };
    return labels[step] || step;
}

function currentStatus(order) {
    return liveStatuses.value[order.id] ?? order.status;
}

function statusColor(status) {
    if (status === 'delivered') return 'text-green-600 bg-green-50 border-green-200';
    if (status === 'cancelled') return 'text-red-600 bg-red-50 border-red-200';
    if (status === 'out_for_delivery') return 'text-blue-600 bg-blue-50 border-blue-200';
    return 'text-yellow-600 bg-yellow-50 border-yellow-200';
}

function showToast(message) {
    toastMessage.value = message;
    if (toastTimeout.value) clearTimeout(toastTimeout.value);
    toastTimeout.value = setTimeout(() => { toastMessage.value = null; }, 5000);
}

onMounted(() => {
    if (!window.Echo || !page.props.auth?.user) return;

    const userId = page.props.auth.user.id;
    echoChannel = window.Echo.private(`orders.${userId}`)
        .listen('.order.status.updated', (data) => {
            liveStatuses.value[data.order_id] = data.status;
            showToast(`Order #${String(data.order_id).padStart(6, '0')}: ${data.message}`);
        });
});

onUnmounted(() => {
    if (echoChannel) echoChannel.stopListening('.order.status.updated');
});
</script>

<template>
    <Head title="My Orders - KartFlip" />
    <MainLayout>
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

            <!-- Flash success -->
            <div v-if="flashSuccess" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                <svg class="h-5 w-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ flashSuccess }}
            </div>

            <!-- Live toast notification -->
            <transition name="slide-down">
                <div v-if="toastMessage" class="fixed top-6 right-6 z-50 bg-indigo-600 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-sm">
                    <span class="text-lg">📦</span>
                    <p class="text-sm font-medium">{{ toastMessage }}</p>
                    <button @click="toastMessage = null" class="ml-auto opacity-70 hover:opacity-100">✕</button>
                </div>
            </transition>

            <div v-if="!orders.data || orders.data.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No orders found</h3>
                <p class="mt-2 text-gray-500">You haven't placed any orders yet.</p>
                <div class="mt-6">
                    <Link :href="route('catalog.index')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Start Shopping
                    </Link>
                </div>
            </div>

            <div v-else class="space-y-6">
                <div v-for="order in orders.data" :key="order.id" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-slate-50 border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-wrap gap-4">
                        <div class="flex gap-8">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Order Placed</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ new Date(order.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">${{ order.total_amount }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a :href="route('orders.invoice', order.id)" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg border border-indigo-200 transition">
                                📄 Invoice PDF
                            </a>
                            <span :class="['text-xs font-bold px-3 py-1 rounded-full border capitalize', statusColor(currentStatus(order))]">
                                {{ (currentStatus(order) === 'cancelled' || order.refund_status === 'refunded') ? 'Cancelled & Refunded' : currentStatus(order).replace('_', ' ') }}
                            </span>
                            <p class="text-sm text-gray-500">ORD-{{ order.id.toString().padStart(6, '0') }}</p>
                        </div>
                    </div>

                    <!-- Delivery Stepper (only for active orders) -->
                    <div v-if="!['cancelled', 'pending'].includes(currentStatus(order))" class="px-6 pt-5 pb-2">
                        <div class="flex items-center">
                            <template v-for="(step, i) in STATUS_STEPS" :key="step">
                                <div class="flex flex-col items-center">
                                    <div :class="[
                                        'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-500',
                                        stepIndex(currentStatus(order)) >= i
                                            ? 'bg-indigo-600 border-indigo-600 text-white'
                                            : 'bg-white border-gray-300 text-gray-400'
                                    ]">
                                        <svg v-if="stepIndex(currentStatus(order)) > i" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span v-else>{{ i + 1 }}</span>
                                    </div>
                                    <p class="text-xs mt-1 text-center w-16" :class="stepIndex(currentStatus(order)) >= i ? 'text-indigo-700 font-semibold' : 'text-gray-400'">
                                        {{ stepLabel(step) }}
                                    </p>
                                </div>
                                <div v-if="i < STATUS_STEPS.length - 1" :class="['flex-1 h-0.5 mb-5 transition-all duration-500', stepIndex(currentStatus(order)) > i ? 'bg-indigo-600' : 'bg-gray-200']" />
                            </template>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="p-6">
                        <ul class="divide-y divide-gray-200">
                            <li v-for="item in order.items" :key="item.id" class="py-4 flex gap-4">
                                <div class="h-20 w-20 flex-shrink-0 border border-gray-200 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <img v-if="item.product.images && item.product.images.length > 0" :src="item.product.images[0].image_url" :alt="item.product.title" class="w-full h-full object-cover" />
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <Link :href="route('product.show', item.product.slug)" class="text-base font-bold text-gray-900 hover:text-blue-600">
                                        {{ item.product.title }}
                                    </Link>
                                    <p class="mt-1 text-sm text-gray-500">Sold by: {{ order.vendor?.store_name || 'KartFlip Vendor' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-medium text-gray-900">${{ item.price }}</p>
                                    <p class="mt-1 text-sm text-gray-500">Qty: {{ item.quantity }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active {
    transition: all 0.3s ease;
}
.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>

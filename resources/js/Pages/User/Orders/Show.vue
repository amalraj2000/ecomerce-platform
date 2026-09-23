<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const liveStatus = ref(props.order.status);
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

function statusColor(status) {
    if (status === 'delivered') return 'text-green-700 bg-green-50 border-green-200';
    if (status === 'cancelled') return 'text-red-700 bg-red-50 border-red-200';
    if (status === 'out_for_delivery') return 'text-blue-700 bg-blue-50 border-blue-200';
    return 'text-amber-700 bg-amber-50 border-amber-200';
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
            if (data.order_id === props.order.id) {
                liveStatus.value = data.status;
                showToast(`Order Status Updated: ${data.message}`);
            }
        });
});

onUnmounted(() => {
    if (echoChannel) echoChannel.stopListening('.order.status.updated');
});
</script>

<template>
    <Head :title="`Order #${order.id} Details - KartFlip`" />

    <MainLayout>
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Toast notification -->
            <transition name="slide-down">
                <div v-if="toastMessage" class="fixed top-6 right-6 z-50 bg-indigo-600 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 max-w-sm">
                    <span class="text-lg">📦</span>
                    <p class="text-sm font-medium">{{ toastMessage }}</p>
                    <button @click="toastMessage = null" class="ml-auto opacity-70 hover:opacity-100">✕</button>
                </div>
            </transition>

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
                <Link href="/" class="hover:text-blue-600 transition-colors">Home</Link>
                <span>/</span>
                <Link :href="route('user.orders')" class="hover:text-blue-600 transition-colors">My Orders</Link>
                <span>/</span>
                <span class="text-slate-900 font-semibold">ORD-{{ String(order.id).padStart(6, '0') }}</span>
            </nav>

            <!-- Order Header Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                <div class="bg-slate-50 border-b border-slate-200 p-6 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h1 class="text-2xl font-bold text-slate-900">Order #{{ String(order.id).padStart(6, '0') }}</h1>
                            <span :class="['text-xs font-bold px-3 py-1 rounded-full border capitalize', statusColor(liveStatus)]">
                                {{ (liveStatus === 'cancelled' || order.refund_status === 'refunded') ? 'Cancelled & Refunded' : liveStatus.replace('_', ' ') }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500">Placed on {{ new Date(order.created_at).toLocaleString('en-US', { dateStyle: 'full', timeStyle: 'short' }) }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a :href="route('orders.invoice', order.id)" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-sm rounded-xl border border-indigo-200 shadow-sm transition">
                            📄 Download Invoice PDF
                        </a>
                    </div>
                </div>

                <!-- Delivery Progress Stepper (Active orders) -->
                <div v-if="!['cancelled', 'pending'].includes(liveStatus)" class="p-8 border-b border-slate-200 bg-white">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-400 mb-6">Delivery Progress</h3>
                    <div class="flex items-center">
                        <template v-for="(step, i) in STATUS_STEPS" :key="step">
                            <div class="flex flex-col items-center relative z-10">
                                <div :class="[
                                    'w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-500',
                                    stepIndex(liveStatus) >= i
                                        ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-100'
                                        : 'bg-white border-slate-300 text-slate-400'
                                ]">
                                    <svg v-if="stepIndex(liveStatus) > i" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span v-else>{{ i + 1 }}</span>
                                </div>
                                <p class="text-xs mt-2 text-center w-20 font-medium" :class="stepIndex(liveStatus) >= i ? 'text-indigo-700 font-bold' : 'text-slate-400'">
                                    {{ stepLabel(step) }}
                                </p>
                            </div>
                            <div v-if="i < STATUS_STEPS.length - 1" :class="['flex-1 h-1 mb-6 transition-all duration-500', stepIndex(liveStatus) > i ? 'bg-indigo-600' : 'bg-slate-200']" />
                        </template>
                    </div>
                </div>

                <!-- Main Content Grid: Items & Sidebar -->
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-slate-200">
                    
                    <!-- Left 2 Cols: Order Items -->
                    <div class="lg:col-span-2 p-6 md:p-8">
                        <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center justify-between">
                            <span>Order Items</span>
                            <span class="text-sm font-normal text-slate-500">{{ order.items?.length || 0 }} item(s)</span>
                        </h2>

                        <ul class="divide-y divide-slate-200">
                            <li v-for="item in order.items" :key="item.id" class="py-5 flex gap-4 first:pt-0 last:pb-0">
                                <div class="h-24 w-24 flex-shrink-0 border border-slate-200 rounded-xl overflow-hidden bg-slate-100 flex items-center justify-center">
                                    <img v-if="item.product?.images && item.product.images.length > 0" :src="item.product.images[0].image_url" :alt="item.product?.title" class="w-full h-full object-cover" />
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <Link :href="route('product.show', item.product?.slug || '#')" class="text-base font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                            {{ item.product?.title }}
                                        </Link>
                                        <p class="text-xs text-slate-500 mt-1">Vendor: <span class="font-medium text-slate-700">{{ order.vendor?.store_name || 'KartFlip Vendor' }}</span></p>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs text-slate-500 font-medium bg-slate-100 px-2.5 py-1 rounded-md">Qty: {{ item.quantity }}</span>
                                        <span class="text-base font-bold text-slate-900">${{ (item.price * item.quantity).toFixed(2) }}</span>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <!-- Price Breakdown -->
                        <div class="mt-8 pt-6 border-t border-slate-200 bg-slate-50 rounded-xl p-5 space-y-3">
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Subtotal</span>
                                <span>${{ order.total_amount }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Shipping</span>
                                <span class="text-green-600 font-semibold">FREE</span>
                            </div>
                            <div class="flex justify-between text-lg font-black text-slate-900 pt-3 border-t border-slate-200">
                                <span>Total Paid</span>
                                <span class="text-indigo-600">${{ order.total_amount }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right 1 Col: Shipping & Payment Info -->
                    <div class="p-6 md:p-8 space-y-6">
                        
                        <!-- Shipping Address -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Shipping Address</h3>
                            <div v-if="order.address" class="bg-slate-50 rounded-xl p-4 border border-slate-200 space-y-1 text-sm">
                                <p class="font-bold text-slate-900">{{ order.address.name }} <span class="ml-1 text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-normal uppercase">{{ order.address.address_type }}</span></p>
                                <p class="text-slate-600">{{ order.address.street }}</p>
                                <p class="text-slate-600">{{ order.address.city }}, {{ order.address.state }} {{ order.address.zip }}</p>
                                <p class="text-slate-600">{{ order.address.country }}</p>
                                <p class="text-slate-600 pt-2 font-medium">📞 {{ order.address.phone }}</p>
                            </div>
                            <p v-else class="text-sm text-slate-400 italic">No address attached</p>
                        </div>

                        <!-- Vendor Details -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Seller Details</h3>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-base">
                                    🏬
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ order.vendor?.store_name || 'KartFlip Store' }}</p>
                                    <p class="text-xs text-slate-500">Verified Marketplace Seller</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment & Refund Status -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Payment Info</h3>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Method</span>
                                    <span class="font-semibold text-slate-900">Stripe Card</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Status</span>
                                    <span :class="['font-bold capitalize', order.refund_status === 'refunded' ? 'text-red-600' : 'text-green-600']">
                                        {{ order.refund_status === 'refunded' ? 'Refunded' : 'Paid' }}
                                    </span>
                                </div>
                                <div v-if="order.stripe_payment_intent_id" class="pt-2 text-xs text-slate-400 truncate">
                                    Ref: {{ order.stripe_payment_intent_id }}
                                </div>
                            </div>
                        </div>

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

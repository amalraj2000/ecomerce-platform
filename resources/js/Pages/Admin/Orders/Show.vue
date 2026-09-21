<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    status: props.order.status,
});

const refundForm = useForm({});

const updateStatus = () => {
    form.put(route('admin.orders.update', props.order.id), {
        preserveScroll: true,
    });
};

const issueRefund = () => {
    if (!confirm('Are you sure you want to issue a full refund for this order? This action cannot be undone.')) return;
    refundForm.post(route('admin.orders.refund', props.order.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Order #${order.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 flex items-center space-x-2">
                    <Link :href="route('admin.orders.index')" class="text-indigo-600 hover:text-indigo-900">Orders</Link>
                    <span class="text-gray-400">/</span>
                    <span>Order #{{ order.id }}</span>
                </h2>
                <span class="capitalize bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold text-sm">
                    {{ order.status }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Order Items -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Order Items</h3>
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="item in order.items" :key="item.id" class="py-4 flex">
                                        <div class="flex-shrink-0 w-20 h-20 border border-gray-200 rounded-md overflow-hidden bg-gray-100">
                                            <div v-if="item.product.images && item.product.images.length > 0" class="w-full h-full">
                                                <img :src="item.product.images[0].image_url" alt="Product Image" class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1 flex flex-col justify-center">
                                            <div class="flex justify-between font-medium text-gray-900">
                                                <h4><Link :href="route('product.show', item.product.slug)" class="hover:text-indigo-600">{{ item.product.title }}</Link></h4>
                                                <p class="ml-4">${{ item.price }}</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">Qty {{ item.quantity }}</p>
                                        </div>
                                    </li>
                                </ul>
                                <div class="mt-6 pt-4 border-t flex justify-end">
                                    <div class="text-right w-64">
                                        <div class="flex justify-between text-lg font-black text-gray-900">
                                            <span>Total</span>
                                            <span>${{ order.total_amount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Details -->
                    <div class="md:col-span-1 space-y-6">
                        
                        <!-- Status Update -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Manage Status</h3>
                                <form @submit.prevent="updateStatus" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Order Status</label>
                                        <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="accepted">Accepted</option>
                                            <option value="processing">Processing</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="out_for_delivery">Out for Delivery</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <PrimaryButton type="submit" :disabled="form.processing" class="w-full justify-center">Update Status</PrimaryButton>
                                </form>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Customer</h3>
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-700 uppercase">
                                        {{ order.user?.name?.charAt(0) || 'U' }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ order.user?.name || 'Unknown User' }}</p>
                                        <p class="text-sm text-gray-500">{{ order.user?.email || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Shipping Address</h3>
                                <div v-if="order.address" class="text-sm space-y-1">
                                    <p class="font-medium text-gray-900">{{ order.address.name }} <span class="bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded text-xs ml-1 uppercase">{{ order.address.address_type }}</span></p>
                                    <p>{{ order.address.street }}</p>
                                    <p>{{ order.address.city }}, {{ order.address.state }} {{ order.address.zip }}</p>
                                    <p>{{ order.address.country }}</p>
                                    <p class="pt-2">Phone: {{ order.address.phone }}</p>
                                </div>
                                <p v-else class="text-sm text-gray-500">No shipping address provided.</p>
                            </div>
                        </div>
                        
                        <!-- Vendor Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Vendor</h3>
                                <p class="font-medium">{{ order.vendor?.store_name || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Refund Panel -->
                        <div v-if="order.stripe_payment_intent_id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Payment & Refund</h3>

                                <div v-if="order.refund_status === 'refunded'" class="flex items-center gap-2 text-green-600 bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-sm font-semibold">Refunded</span>
                                </div>

                                <div v-else-if="order.refund_status === 'requested'" class="flex items-center gap-2 text-yellow-600 bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2 mb-4">
                                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    <span class="text-sm font-semibold">Refund Processing…</span>
                                </div>

                                <button
                                    v-else
                                    @click="issueRefund"
                                    :disabled="refundForm.processing"
                                    class="w-full bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
                                >
                                    {{ refundForm.processing ? 'Processing…' : 'Issue Full Refund' }}
                                </button>

                                <p class="text-xs text-gray-400 mt-2">Refund will be processed via Stripe and may take 5-10 business days.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

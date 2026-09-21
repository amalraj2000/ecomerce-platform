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

const updateStatus = () => {
    form.put(route('vendor.orders.update', props.order.id), {
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
                    <Link :href="route('vendor.orders.index')" class="text-indigo-600 hover:text-indigo-900">Orders</Link>
                    <span class="text-gray-400">/</span>
                    <span>Order #{{ order.id }}</span>
                </h2>
                <div class="flex items-center space-x-3">
                    <a :href="route('orders.invoice', order.id)" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-gray-50 text-indigo-600 font-semibold text-xs rounded-xl border border-indigo-200 shadow-sm transition">
                        📄 Invoice PDF
                    </a>
                    <span
                        :class="[
                            'capitalize px-3 py-1 rounded-full font-bold text-sm',
                            (order.status === 'cancelled' || order.refund_status === 'refunded') ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'
                        ]"
                    >
                        {{ (order.status === 'cancelled' || order.refund_status === 'refunded') ? 'Cancelled & Refunded' : order.status.replace('_', ' ') }}
                    </span>
                </div>
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

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    orders: Object
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
</script>

<template>
    <Head title="My Orders - KartFlip" />

    <MainLayout>
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

            <!-- Success Message -->
            <div v-if="flashSuccess" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                <svg class="h-5 w-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ flashSuccess }}
            </div>

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
                <!-- Order Card -->
                <div v-for="order in orders.data" :key="order.id" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
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
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Ship To</p>
                                <p class="text-sm text-blue-600 font-medium mt-1 hover:underline cursor-pointer">Default Address</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Order #</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">ORD-{{ order.id.toString().padStart(6, '0') }}</p>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 capitalize" 
                                :class="{'text-green-600': order.status === 'delivered', 'text-yellow-600': order.status === 'pending' || order.status === 'processing'}">
                                Status: {{ order.status }}
                            </h3>
                        </div>
                        
                        <ul class="divide-y divide-gray-200">
                            <li v-for="item in order.items" :key="item.id" class="py-4 flex gap-4">
                                <div class="h-20 w-20 flex-shrink-0 border border-gray-200 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <div v-if="item.product.images && item.product.images.length > 0" class="w-full h-full">
                                        <img :src="item.product.images[0].image_url" alt="Product Image" class="w-full h-full object-cover" />
                                    </div>
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

                <!-- Pagination -->
                <div class="mt-6">
                    <!-- Basic Pagination component mockup for demonstration -->
                </div>
            </div>
        </div>
    </MainLayout>
</template>

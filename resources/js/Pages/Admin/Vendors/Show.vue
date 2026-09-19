<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    vendor: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <Head :title="vendor.store_name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('admin.vendors.index')" class="text-gray-500 hover:text-gray-700">
                    &larr; Back to Vendors
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Vendor: {{ vendor.store_name }}
                </h2>
                <span v-if="vendor.is_verified" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Verified</span>
                <span v-else class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Pending</span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Vendor Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vendor Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Owner Name</p>
                            <p class="font-medium text-gray-900">{{ vendor.user?.name || 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ vendor.user?.email || 'Unknown' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500">Description</p>
                            <p class="text-gray-900">{{ vendor.description || 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Products ({{ vendor.products.length }})</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="py-3 px-4 font-medium text-gray-900">Title</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Category</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Price</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in vendor.products" :key="product.id" class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ product.title }}</td>
                                    <td class="py-3 px-4">{{ product.category?.name || 'N/A' }}</td>
                                    <td class="py-3 px-4">${{ product.price }}</td>
                                    <td class="py-3 px-4">{{ product.stock }}</td>
                                </tr>
                                <tr v-if="vendor.products.length === 0">
                                    <td colspan="4" class="py-6 text-center text-gray-500">No products found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Orders ({{ vendor.orders.length }})</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="py-3 px-4 font-medium text-gray-900">Order ID</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Customer</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Total Amount</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Status</th>
                                    <th class="py-3 px-4 font-medium text-gray-900">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in vendor.orders" :key="order.id" class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">#{{ order.id }}</td>
                                    <td class="py-3 px-4">{{ order.user?.name || 'Unknown' }}</td>
                                    <td class="py-3 px-4">${{ order.total_amount }}</td>
                                    <td class="py-3 px-4">
                                        <span class="capitalize px-2 py-1 rounded-full text-xs"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                                'bg-blue-100 text-blue-800': order.status === 'processing',
                                                'bg-green-100 text-green-800': order.status === 'completed',
                                                'bg-red-100 text-red-800': order.status === 'cancelled',
                                            }">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-500 text-sm">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                                </tr>
                                <tr v-if="vendor.orders.length === 0">
                                    <td colspan="5" class="py-6 text-center text-gray-500">No orders found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

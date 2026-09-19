<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    orders: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Manage Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Orders
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-4 px-6 font-medium text-gray-900">Order ID</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Customer</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Vendor</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Total</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orders.data" :key="order.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-medium">#{{ order.id }}</td>
                                    <td class="py-4 px-6">{{ order.user?.name || 'N/A' }}</td>
                                    <td class="py-4 px-6">{{ order.vendor?.store_name || 'N/A' }}</td>
                                    <td class="py-4 px-6">${{ order.total_amount }}</td>
                                    <td class="py-4 px-6">
                                        <span class="capitalize bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ order.status }}</span>
                                    </td>
                                </tr>
                                <tr v-if="orders.data.length === 0">
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        No orders found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

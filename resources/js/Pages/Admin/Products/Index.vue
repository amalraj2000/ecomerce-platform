<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    products: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Manage Products" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Products
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-4 px-6 font-medium text-gray-900">Title</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Category</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Vendor</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Price</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products.data" :key="product.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6">{{ product.title }}</td>
                                    <td class="py-4 px-6">{{ product.category?.name || 'N/A' }}</td>
                                    <td class="py-4 px-6">{{ product.vendor?.store_name || 'N/A' }}</td>
                                    <td class="py-4 px-6">${{ product.price }}</td>
                                    <td class="py-4 px-6">{{ product.stock }}</td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        No products found.
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

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';

defineProps({
    products: {
        type: Object,
        required: true,
    },
});

const deleteProduct = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('vendor.products.destroy', id));
        }
    });
};

const toggleStatus = (product) => {
    router.put(route('vendor.products.update', product.id), {
        status: product.status === 'active' ? 'inactive' : 'active'
    }, { preserveScroll: true });
};
</script>

<template>
    <Head title="My Store Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Products
                </h2>
                <Link :href="route('vendor.products.create')">
                    <PrimaryButton>
                        Add New Product
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-4 px-6 font-medium text-gray-900">Image</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Title</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Category</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Price</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Stock</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Status</th>
                                    <th class="py-4 px-6 font-medium text-gray-900 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products.data" :key="product.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6">
                                        <img v-if="product.images && product.images.length > 0" :src="product.images[0].image_url" alt="Product" class="h-10 w-10 rounded object-cover border border-gray-200">
                                        <div v-else class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">No img</div>
                                    </td>
                                    <td class="py-4 px-6 font-medium">{{ product.title }}</td>
                                    <td class="py-4 px-6">{{ product.category?.name || 'N/A' }}</td>
                                    <td class="py-4 px-6">${{ product.price }}</td>
                                    <td class="py-4 px-6">
                                        <span :class="product.stock > 10 ? 'text-green-600' : 'text-red-600'">{{ product.stock }} in stock</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="capitalize text-xs px-2 py-1 rounded-full cursor-pointer" @click="toggleStatus(product)" :class="product.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ product.status || 'active' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <Link :href="route('vendor.products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</Link>
                                        <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td colspan="7" class="py-12 text-center text-gray-500">
                                        You haven't added any products yet.
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

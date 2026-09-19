<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineProps({
    vendors: {
        type: Object,
        required: true,
    },
});

const toggleStatus = (vendor) => {
    router.put(route('admin.vendors.update', vendor.id), {
        status: vendor.status === 'active' ? 'inactive' : 'active'
    }, { preserveScroll: true });
};

const deleteVendor = (id) => {
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
            router.delete(route('admin.vendors.destroy', id));
        }
    });
};
</script>

<template>
    <Head title="Manage Vendors" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Vendors
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-4 px-6 font-medium text-gray-900">Store Name</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Owner</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Verification</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Status</th>
                                    <th class="py-4 px-6 font-medium text-gray-900 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="vendor in vendors.data" :key="vendor.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6">{{ vendor.store_name }}</td>
                                    <td class="py-4 px-6">{{ vendor.user?.name || 'N/A' }}</td>
                                    <td class="py-4 px-6">
                                        <span v-if="vendor.is_verified" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Verified</span>
                                        <span v-else class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Pending</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="capitalize text-xs px-2 py-1 rounded-full cursor-pointer" @click="toggleStatus(vendor)" :class="vendor.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            {{ vendor.status || 'active' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <Link :href="route('admin.vendors.show', vendor.id)" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</Link>
                                        <Link :href="route('admin.vendors.edit', vendor.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</Link>
                                        <button @click="deleteVendor(vendor.id)" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="vendors.data.length === 0">
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        No vendors found.
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

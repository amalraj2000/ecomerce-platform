<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';

defineProps({
    vendors: {
        type: Object,
        required: true,
    },
});

const approveVendor = (id) => {
    Swal.fire({
        title: 'Approve Vendor?',
        text: "This vendor will now be able to log in and sell products.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, approve them!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.vendors.approve', id));
        }
    });
};
</script>

<template>
    <Head title="Pending Vendors" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Pending Vendors
                </h2>
                <Link :href="route('admin.vendors.index')" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Back to Approved Vendors
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="py-4 px-6 font-medium text-gray-900">Store Name</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Owner Name</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Owner Email</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Description</th>
                                    <th class="py-4 px-6 font-medium text-gray-900 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="vendor in vendors.data" :key="vendor.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-medium">{{ vendor.store_name }}</td>
                                    <td class="py-4 px-6">{{ vendor.user?.name || 'N/A' }}</td>
                                    <td class="py-4 px-6">{{ vendor.user?.email || 'N/A' }}</td>
                                    <td class="py-4 px-6 max-w-xs truncate text-gray-500">{{ vendor.description || 'No description provided' }}</td>
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <button @click="approveVendor(vendor.id)" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-medium transition">
                                            Approve
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="vendors.data.length === 0">
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        No pending vendor registrations.
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

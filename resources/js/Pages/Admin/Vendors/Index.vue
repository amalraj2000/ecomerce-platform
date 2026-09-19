<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    vendors: {
        type: Object,
        required: true,
    },
});
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
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <Link :href="route('admin.vendors.show', vendor.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</Link>
                                    </td>
                                </tr>
                                <tr v-if="vendors.data.length === 0">
                                    <td colspan="4" class="py-8 text-center text-gray-500">
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

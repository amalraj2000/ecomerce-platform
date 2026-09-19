<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Swal from 'sweetalert2';

defineProps({
    categories: {
        type: Object,
        required: true,
    },
});

const deleteCategory = (id) => {
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
            router.delete(route('admin.categories.destroy', id));
        }
    });
};
</script>

<template>
    <Head title="Manage Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Categories
                </h2>
                <Link :href="route('admin.categories.create')">
                    <PrimaryButton>
                        Add New Category
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
                                    <th class="py-4 px-6 font-medium text-gray-900">Name</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Slug</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Parent Category</th>
                                    <th class="py-4 px-6 font-medium text-gray-900">Created At</th>
                                    <th class="py-4 px-6 font-medium text-gray-900 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="category in categories.data" :key="category.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6">{{ category.name }}</td>
                                    <td class="py-4 px-6 text-gray-500">{{ category.slug }}</td>
                                    <td class="py-4 px-6">
                                        <span v-if="category.parent" class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">
                                            {{ category.parent.name }}
                                        </span>
                                        <span v-else class="text-gray-400 text-sm">None</span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 text-sm">{{ new Date(category.created_at).toLocaleDateString() }}</td>
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <Link :href="route('admin.categories.edit', category.id)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</Link>
                                        <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="categories.data.length === 0">
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        No categories found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Simple Pagination -->
                        <div v-if="categories.links && categories.data.length > 0" class="mt-6 flex justify-between items-center text-sm text-gray-600">
                            <div>
                                Showing {{ categories.from }} to {{ categories.to }} of {{ categories.total }} entries
                            </div>
                            <div class="flex space-x-2">
                                <Link 
                                    v-for="(link, index) in categories.links" 
                                    :key="index"
                                    :href="link.url"
                                    class="px-4 py-2 border rounded-md"
                                    :class="link.active ? 'bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white hover:bg-gray-50'"
                                    v-html="link.label"
                                ></Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

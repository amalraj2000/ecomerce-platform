<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Admin Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Welcome Section -->
                <div class="mb-8 p-6 bg-white shadow-sm sm:rounded-xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Welcome back, {{ $page.props.auth.user.name }}!</h3>
                        <p class="text-gray-500 mt-1">Here is a summary of your platform's statistics.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Orders Stat -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total Orders</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ stats.orders }}</h4>
                        </div>
                    </div>

                    <!-- Vendors Stat -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Vendors</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ stats.vendors }}</h4>
                        </div>
                    </div>

                    <!-- Users Stat -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Customers</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ stats.users }}</h4>
                        </div>
                    </div>

                    <!-- Products Stat -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Products</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ stats.products }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link :href="route('admin.vendors.pending')" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600">Pending Vendors &rarr;</h4>
                        <p class="text-sm text-gray-500 mt-1">Review and approve new vendor registrations</p>
                    </Link>
                    <Link :href="route('admin.orders.index')" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600">Manage Orders &rarr;</h4>
                        <p class="text-sm text-gray-500 mt-1">View and update customer order statuses</p>
                    </Link>
                    <Link :href="route('admin.categories.index')" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600">Categories &rarr;</h4>
                        <p class="text-sm text-gray-500 mt-1">Organize the product catalog categories</p>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

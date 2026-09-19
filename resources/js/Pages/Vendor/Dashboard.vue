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
    <Head title="Vendor Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Vendor Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Welcome Section -->
                <div class="mb-8 p-6 bg-white shadow-sm sm:rounded-xl border border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Welcome to your Store, {{ $page.props.auth.user.name }}!</h3>
                        <p class="text-gray-500 mt-1">Here is a summary of your store's performance.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Products Stat -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">My Products</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ stats.products }}</h4>
                        </div>
                    </div>

                    <!-- Orders Stat -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300"></div>
                        <div class="relative z-10">
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Store Orders</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ stats.orders }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Link :href="route('vendor.products.index')" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600">Manage Products &rarr;</h4>
                        <p class="text-sm text-gray-500 mt-1">Add, edit, or remove products from your store catalog.</p>
                    </Link>
                    <Link :href="route('vendor.orders.index')" class="block p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition group">
                        <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600">View Orders &rarr;</h4>
                        <p class="text-sm text-gray-500 mt-1">Track and manage customer orders placed at your store.</p>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

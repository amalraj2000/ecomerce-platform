<template>
    <div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-100 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
            <div class="mx-auto mb-6 w-20 h-20 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Cancelled</h1>
            <p class="text-gray-500 mb-6">
                No worries — your cart is still saved. You can complete your purchase anytime.
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                <Link :href="route('checkout.index')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors duration-200">
                    Try Again
                </Link>
                <Link :href="route('cart.index')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-colors duration-200">
                    Back to Cart
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

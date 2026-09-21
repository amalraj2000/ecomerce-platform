<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
            <!-- Animated checkmark -->
            <div class="mx-auto mb-6 w-20 h-20 rounded-full bg-green-100 flex items-center justify-center animate-bounce-once">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Successful! 🎉</h1>
            <p class="text-gray-500 mb-6">
                Your payment has been confirmed. We're preparing your order and will notify you in real-time as it progresses.
            </p>

            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-left">
                <p class="text-sm text-green-700 font-medium">What happens next?</p>
                <ul class="mt-2 space-y-1 text-sm text-green-600">
                    <li>✓ Order confirmed &amp; sent to vendor</li>
                    <li>✓ You'll see live status updates on your orders page</li>
                    <li>✓ Track delivery in real-time</li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <Link :href="route('user.orders')" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors duration-200">
                    View My Orders
                </Link>
                <Link :href="route('home')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-colors duration-200">
                    Continue Shopping
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    sessionId: String,
});
</script>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cart: Object
});

const getPrice = (item) => {
    return item.product.price * (1 - item.product.discount_percentage / 100);
};

const subtotal = computed(() => {
    if (!props.cart || !props.cart.items) return 0;
    return props.cart.items.reduce((total, item) => total + (getPrice(item) * item.quantity), 0);
});

const checkoutForm = useForm({
    shipping_address: '123 Main St, City, Country', // Mocked for now
});

const placeOrder = () => {
    checkoutForm.post(route('checkout.store'));
};
</script>

<template>
    <Head title="Checkout - KartFlip" />

    <MainLayout>
        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            <div v-if="!cart || cart.items.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p>Your cart is empty. Cannot proceed to checkout.</p>
                <Link :href="route('catalog.index')" class="text-blue-600">Back to Shopping</Link>
            </div>
            
            <div v-else>
                <form @submit.prevent="placeOrder" class="space-y-8">
                    <!-- Shipping Address (Mocked) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <p class="font-medium text-slate-800">Default Shipping Address</p>
                            <p class="text-slate-600">{{ checkoutForm.shipping_address }}</p>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                        <ul class="divide-y divide-gray-200 mb-4">
                            <li v-for="item in cart.items" :key="item.id" class="py-3 flex justify-between">
                                <div>
                                    <span class="font-medium text-gray-900">{{ item.product.title }}</span>
                                    <span class="text-gray-500 ml-2">x {{ item.quantity }}</span>
                                </div>
                                <span class="text-gray-900">${{ (getPrice(item) * item.quantity).toFixed(2) }}</span>
                            </li>
                        </ul>
                        <div class="border-t border-gray-200 pt-4 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span>${{ subtotal.toFixed(2) }}</span>
                        </div>
                    </div>
                    
                    <button type="submit" :disabled="checkoutForm.processing" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-sm transition-all text-lg disabled:opacity-50">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </MainLayout>
</template>

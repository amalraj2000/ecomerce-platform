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

const removeItemForm = useForm({});

const removeItem = (itemId) => {
    if (confirm('Remove item from cart?')) {
        removeItemForm.delete(route('cart.destroy', itemId));
    }
};
</script>

<template>
    <Head title="Your Cart - KartFlip" />

    <MainLayout>
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

            <div v-if="!cart || cart.items.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
                <p class="mt-2 text-gray-500">Looks like you haven't added anything to your cart yet.</p>
                <div class="mt-6">
                    <Link :href="route('catalog.index')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Continue Shopping
                    </Link>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <ul role="list" class="divide-y divide-gray-200">
                            <li v-for="item in cart.items" :key="item.id" class="p-6 flex py-6">
                                <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden bg-gray-100">
                                    <div v-if="item.product.images && item.product.images.length > 0" class="w-full h-full">
                                        <img :src="item.product.images[0].image_url" alt="Product Image" class="w-full h-full object-cover" />
                                    </div>
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                </div>

                                <div class="ml-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>
                                                <Link :href="route('product.show', item.product.slug)">{{ item.product.title }}</Link>
                                            </h3>
                                            <p class="ml-4">${{ (getPrice(item) * item.quantity).toFixed(2) }}</p>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">${{ getPrice(item).toFixed(2) }} each</p>
                                        <p v-if="item.color || item.size" class="mt-1 text-xs text-gray-500">
                                            <span v-if="item.color">Color: {{ item.color }}</span>
                                            <span v-if="item.color && item.size"> | </span>
                                            <span v-if="item.size">Size: {{ item.size }}</span>
                                        </p>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <div class="flex items-center space-x-3">
                                            <p class="text-gray-500">Qty {{ item.quantity }}</p>
                                        </div>

                                        <div class="flex">
                                            <button @click="removeItem(item.id)" type="button" class="font-medium text-red-600 hover:text-red-500">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <div class="bg-slate-50 rounded-xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>
                        <div class="flow-root">
                            <dl class="-my-4 text-sm divide-y divide-gray-200">
                                <div class="py-4 flex items-center justify-between">
                                    <dt class="text-gray-600">Subtotal</dt>
                                    <dd class="font-medium text-gray-900">${{ subtotal.toFixed(2) }}</dd>
                                </div>
                                <div class="py-4 flex items-center justify-between">
                                    <dt class="text-gray-600">Shipping</dt>
                                    <dd class="font-medium text-gray-900">Calculated at checkout</dd>
                                </div>
                                <div class="py-4 flex items-center justify-between">
                                    <dt class="text-base font-bold text-gray-900">Total</dt>
                                    <dd class="text-base font-bold text-gray-900">${{ subtotal.toFixed(2) }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="mt-6">
                            <Link :href="route('checkout.index')" class="w-full flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Proceed to Checkout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

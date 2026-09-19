<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    wishlist: Object
});

const getPrice = (item) => {
    return item.product.price * (1 - item.product.discount_percentage / 100);
};

// No subtotal needed for wishlist

const removeItemForm = useForm({});

const removeItem = (itemId) => {
    if (confirm('Remove item from wishlist?')) {
        removeItemForm.delete(route('wishlist.destroy', itemId));
    }
};

const moveToCartForm = useForm({
    product_id: null,
    quantity: 1,
});

const moveToCart = (item) => {
    moveToCartForm.product_id = item.product.id;
    moveToCartForm.post(route('cart.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // After successfully adding to cart, remove from wishlist
            removeItemForm.delete(route('wishlist.destroy', item.id));
        }
    });
};
</script>

<template>
    <Head title="Your Wishlist - KartFlip" />

    <MainLayout>
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>

            <div v-if="!wishlist || !wishlist.items || wishlist.items.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Your wishlist is empty</h3>
                <p class="mt-2 text-gray-500">Save items you like to your wishlist.</p>
                <div class="mt-6">
                    <Link :href="route('catalog.index')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Continue Shopping
                    </Link>
                </div>
            </div>

            <div v-else class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="item in wishlist.items" :key="item.id" class="p-6 flex py-6">
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
                                            <p class="ml-4">${{ getPrice(item).toFixed(2) }}</p>
                                        </div>
                                        <div v-if="item.added_price > getPrice(item)" class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Price dropped since you added it!
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm mt-4">
                                        <div class="flex">
                                            <button @click="removeItem(item.id)" type="button" class="font-medium text-red-600 hover:text-red-500 mr-4">
                                                Remove
                                            </button>
                                            <button @click="moveToCart(item)" :disabled="moveToCartForm.processing" type="button" class="font-medium text-blue-600 hover:text-blue-500">
                                                Move to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
        </div>
    </MainLayout>
</template>

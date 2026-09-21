<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    wishlist: Object,
});

const removeFromWishlist = (product) => {
    router.post(route('wishlist.toggle', product.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Removed from wishlist',
                showConfirmButton: false,
                timer: 2000,
            });
        },
    });
};

const addToCart = (product) => {
    router.post(route('cart.store'), {
        product_id: product.id,
        quantity: 1,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Added to cart!',
                showConfirmButton: false,
                timer: 2000,
            });
        },
    });
};
</script>

<template>
    <Head title="My Wishlist - KartFlip" />

    <MainLayout>
        <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8 flex items-center gap-3">
                <span class="text-red-500">❤️</span> My Wishlist
            </h1>

            <div v-if="!wishlist || !wishlist.items || wishlist.items.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-red-50 text-red-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    💔
                </div>
                <h3 class="text-lg font-bold text-gray-800">Your wishlist is empty</h3>
                <p class="text-gray-500 mt-1 mb-6 text-sm">Explore products and save your favorites here.</p>
                <Link :href="route('catalog.index')" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl transition shadow-sm">
                    Browse Products
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div
                    v-for="item in wishlist.items"
                    :key="item.id"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition duration-200 flex flex-col justify-between"
                >
                    <div>
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            <img
                                v-if="item.product?.images && item.product.images.length > 0"
                                :src="item.product.images[0].image_url"
                                :alt="item.product?.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                🖼️
                            </div>
                            <button
                                @click="removeFromWishlist(item.product)"
                                class="absolute top-3 right-3 w-8 h-8 bg-white/90 hover:bg-red-50 hover:text-red-500 text-gray-400 rounded-full flex items-center justify-center transition shadow-sm"
                                title="Remove from wishlist"
                            >
                                ✕
                            </button>
                        </div>

                        <div class="p-4">
                            <Link :href="route('product.show', item.product?.slug || '#')" class="font-bold text-gray-900 hover:text-indigo-600 line-clamp-1">
                                {{ item.product?.title }}
                            </Link>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ item.product?.description }}</p>
                            
                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-lg font-black text-gray-900">
                                    ${{ (item.product?.price * (1 - (item.product?.discount_percentage || 0) / 100)).toFixed(2) }}
                                </span>
                                <span v-if="item.product?.discount_percentage > 0" class="text-xs text-gray-400 line-through">
                                    ${{ item.product?.price }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 pt-0">
                        <button
                            @click="addToCart(item.product)"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-2 shadow-sm"
                        >
                            <span>🛒</span> Move to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

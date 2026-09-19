<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
  topDeals: Array,
  trending: Array,
  categories: Array
});
</script>

<template>
  <Head title="KartFlip - Online Shopping" />

  <MainLayout>
    <!-- Promotional Banner Placeholder (Using Tailwind for quick styling) -->
    <div class="relative w-full h-64 md:h-96 rounded-2xl overflow-hidden mb-12 shadow-xl bg-gradient-to-r from-blue-600 to-indigo-700 flex items-center justify-center text-white">
      <div class="text-center">
        <h1 class="text-4xl md:text-6xl font-black mb-4">Big Billion Days</h1>
        <p class="text-lg md:text-2xl font-medium">Up to 80% off on Top Brands!</p>
        <button class="mt-8 px-8 py-3 bg-white text-blue-600 font-bold rounded-full hover:bg-slate-100 transition-colors shadow-lg">
          Shop Now
        </button>
      </div>
      <!-- Decorative elements -->
      <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full mix-blend-overlay filter blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full mix-blend-overlay filter blur-3xl translate-x-1/3 translate-y-1/3"></div>
    </div>

    <!-- Top Deals Section -->
    <section class="mb-16">
      <div class="flex justify-between items-end mb-6">
        <div>
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Top Deals of the Day</h2>
          <p class="text-slate-500 dark:text-slate-400 mt-1">Don't miss out on these limited-time offers</p>
        </div>
        <Link href="/catalog?deal=top" class="text-blue-600 font-semibold hover:text-blue-700">View All</Link>
      </div>
      
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div v-if="topDeals.length === 0" class="col-span-full py-12 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
          No deals available right now. Check back later!
        </div>
        
        <Link v-for="product in topDeals" :key="product.id" :href="route('product.show', product.slug)" class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <div class="aspect-square bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
             <div v-if="product.images && product.images.length > 0" class="absolute inset-0">
               <img :src="product.images[0].image_url" alt="Product Image" class="w-full h-full object-cover" />
             </div>
             <!-- Placeholder for image -->
             <div v-else class="absolute inset-0 flex items-center justify-center text-slate-400">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
               </svg>
             </div>
             <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
               {{ product.discount_percentage }}% OFF
             </span>
          </div>
          <div class="p-4">
            <h3 class="font-medium text-slate-900 dark:text-white truncate group-hover:text-blue-600 transition-colors">{{ product.title }}</h3>
            <div class="mt-2 flex items-baseline space-x-2">
              <span class="text-lg font-bold text-slate-900 dark:text-white">${{ (product.price * (1 - product.discount_percentage / 100)).toFixed(2) }}</span>
              <span class="text-sm text-slate-500 line-through">${{ product.price }}</span>
            </div>
          </div>
        </Link>
      </div>
    </section>

    <!-- Trending Products -->
    <section class="mb-16">
      <div class="flex justify-between items-end mb-6">
        <div>
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Trending Now</h2>
          <p class="text-slate-500 dark:text-slate-400 mt-1">What everyone is buying right now</p>
        </div>
        <Link href="/catalog?sort=popular" class="text-blue-600 font-semibold hover:text-blue-700">Explore</Link>
      </div>
      
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <div v-if="trending.length === 0" class="col-span-full py-12 text-center text-slate-500 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
          No trending products yet. Check back later!
        </div>
        
        <Link v-for="product in trending" :key="product.id" :href="route('product.show', product.slug)" class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
          <div class="aspect-square bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
             <div v-if="product.images && product.images.length > 0" class="absolute inset-0">
               <img :src="product.images[0].image_url" alt="Product Image" class="w-full h-full object-cover" />
             </div>
             <div v-else class="absolute inset-0 flex items-center justify-center text-slate-400">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
               </svg>
             </div>
          </div>
          <div class="p-4">
            <h3 class="font-medium text-slate-900 dark:text-white truncate group-hover:text-blue-600 transition-colors">{{ product.title }}</h3>
            <div class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
              ${{ product.price }}
            </div>
          </div>
        </Link>
      </div>
    </section>

  </MainLayout>
</template>

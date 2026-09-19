<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const min_price = ref(props.filters.min_price || '');
const max_price = ref(props.filters.max_price || '');
const sort = ref(props.filters.sort || 'newest');
const viewMode = ref('grid'); // grid or list

const applyFilters = () => {
  router.get('/catalog', {
    search: search.value,
    category: category.value,
    min_price: min_price.value,
    max_price: max_price.value,
    sort: sort.value
  }, { preserveState: true, preserveScroll: true });
};

// Auto-apply filters when sorting changes
watch(sort, applyFilters);

// Update local state when URL/props change
watch(() => props.filters, (newFilters) => {
  category.value = newFilters.category || '';
  search.value = newFilters.search || '';
  min_price.value = newFilters.min_price || '';
  max_price.value = newFilters.max_price || '';
}, { deep: true });

const wishlistForm = useForm({
  product_id: null
});

const addToWishlist = (productId) => {
  wishlistForm.product_id = productId;
  wishlistForm.post(route('wishlist.store'), {
    preserveScroll: true
  });
};
</script>

<template>
  <Head title="KartFlip - Catalog" />

  <MainLayout>
    <div class="flex flex-col md:flex-row gap-8">
      
      <!-- Filters Sidebar -->
      <aside class="w-full md:w-64 flex-shrink-0">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 sticky top-24">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Filters</h2>
            <button @click="router.get('/catalog')" class="text-sm text-blue-600 hover:text-blue-700">Clear all</button>
          </div>

          <!-- Category Filter -->
          <div class="mb-6">
            <h3 class="font-semibold text-slate-900 dark:text-white mb-3 text-sm uppercase tracking-wider">Category</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto no-scrollbar pr-2">
              <label class="flex items-center space-x-3 cursor-pointer">
                <input type="radio" v-model="category" value="" @change="applyFilters" class="form-radio text-blue-600 focus:ring-blue-500 bg-slate-100 border-slate-300 dark:bg-slate-800 dark:border-slate-700 h-4 w-4">
                <span class="text-slate-700 dark:text-slate-300">All Categories</span>
              </label>
              <label v-for="cat in categories" :key="cat.id" class="flex items-center space-x-3 cursor-pointer">
                <input type="radio" v-model="category" :value="cat.slug" @change="applyFilters" class="form-radio text-blue-600 focus:ring-blue-500 bg-slate-100 border-slate-300 dark:bg-slate-800 dark:border-slate-700 h-4 w-4">
                <span class="text-slate-700 dark:text-slate-300">{{ cat.name }}</span>
              </label>
            </div>
          </div>

          <!-- Price Filter -->
          <div class="mb-6">
            <h3 class="font-semibold text-slate-900 dark:text-white mb-3 text-sm uppercase tracking-wider">Price Range</h3>
            <div class="flex items-center space-x-2">
              <input type="number" v-model="min_price" placeholder="Min" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <span class="text-slate-500">-</span>
              <input type="number" v-model="max_price" placeholder="Max" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button @click="applyFilters" class="mt-3 w-full bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 py-2 rounded-md font-medium text-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors">Apply</button>
          </div>
        </div>
      </aside>

      <!-- Main Content -->
      <div class="flex-1">
        
        <!-- Header & Sort -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
          <div class="mb-4 sm:mb-0 text-slate-600 dark:text-slate-400">
            Showing <span class="font-semibold text-slate-900 dark:text-white">{{ products.from || 0 }} - {{ products.to || 0 }}</span> of <span class="font-semibold text-slate-900 dark:text-white">{{ products.total }}</span> results
            <span v-if="search"> for "<span class="font-semibold text-slate-900 dark:text-white">{{ search }}</span>"</span>
          </div>

          <div class="flex items-center space-x-4">
            <!-- View Toggle -->
            <div class="hidden sm:flex bg-slate-100 dark:bg-slate-800 rounded-lg p-1 border border-slate-200 dark:border-slate-700">
              <button @click="viewMode = 'grid'" :class="{'bg-white dark:bg-slate-700 shadow': viewMode === 'grid', 'text-slate-500': viewMode !== 'grid'}" class="p-1.5 rounded-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
              </button>
              <button @click="viewMode = 'list'" :class="{'bg-white dark:bg-slate-700 shadow': viewMode === 'list', 'text-slate-500': viewMode !== 'list'}" class="p-1.5 rounded-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
            </div>

            <!-- Sort Dropdown -->
            <select v-model="sort" class="form-select bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-lg py-2 pl-3 pr-10 focus:ring-blue-500 focus:border-blue-500">
              <option value="newest">Newest First</option>
              <option value="popularity">Popularity</option>
              <option value="price_asc">Price -- Low to High</option>
              <option value="price_desc">Price -- High to Low</option>
            </select>
          </div>
        </div>

        <!-- Product Grid/List -->
        <div v-if="products.data.length === 0" class="py-20 text-center bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">No products found</h3>
          <p class="text-slate-500 mt-2">Try adjusting your filters or search query.</p>
          <button @click="router.get('/catalog')" class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Clear Filters</button>
        </div>
        
        <div v-else :class="{'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6': viewMode === 'grid', 'space-y-6': viewMode === 'list'}">
          <Link v-for="product in products.data" :key="product.id" :href="route('product.show', product.slug)" 
               class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
               :class="{'flex flex-row': viewMode === 'list', 'flex flex-col': viewMode === 'grid'}">
            
            <div class="bg-slate-100 dark:bg-slate-800 relative overflow-hidden" :class="{'aspect-square': viewMode === 'grid', 'w-48 h-48 flex-shrink-0': viewMode === 'list'}">
               <div v-if="product.images && product.images.length > 0" class="absolute inset-0">
                 <img :src="product.images[0].image_url" alt="Product Image" class="w-full h-full object-cover" />
               </div>
               <div v-else class="absolute inset-0 flex items-center justify-center text-slate-400">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                 </svg>
               </div>
               
               <!-- Out of Stock Badge -->
               <div v-if="product.stock <= 0" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 flex items-center justify-center z-10 backdrop-blur-[2px]">
                   <span class="bg-red-600 text-white font-black px-3 py-1.5 uppercase tracking-widest shadow-lg rounded-sm text-xs md:text-sm border-2 border-red-700">Out of Stock</span>
               </div>
               
               <span v-if="product.discount_percentage > 0 && product.stock > 0" class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded z-20">
                 {{ product.discount_percentage }}% OFF
               </span>

               <!-- Wishlist Button -->
               <button @click.prevent="addToWishlist(product.id)" class="absolute top-2 right-2 bg-white/80 backdrop-blur-sm dark:bg-slate-900/80 p-2 rounded-full text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-800 transition-all z-20 shadow-sm" title="Add to Wishlist">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                 </svg>
               </button>
            </div>

            <div class="p-5 flex flex-col justify-between flex-1">
              <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ product.vendor?.store_name || 'Vendor' }}</div>
                <h3 class="font-medium text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors" :class="{'line-clamp-2': viewMode === 'grid', 'text-lg': viewMode === 'list'}">{{ product.title }}</h3>
                <p v-if="viewMode === 'list'" class="text-sm text-slate-500 mt-2 line-clamp-2">{{ product.description }}</p>
              </div>
              
              <div class="mt-4 flex items-center justify-between">
                <div class="flex flex-col">
                  <span class="text-xl font-bold text-slate-900 dark:text-white">${{ (product.price * (1 - product.discount_percentage / 100)).toFixed(2) }}</span>
                  <span v-if="product.discount_percentage > 0" class="text-sm text-slate-500 line-through">${{ product.price }}</span>
                </div>
                
                <div class="flex items-center text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 px-2 py-1 rounded-lg">
                  <span class="text-sm font-bold text-slate-700 dark:text-yellow-500 mr-1">4.5</span>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
              </div>
            </div>
          </Link>
        </div>

        <!-- Pagination -->
        <div v-if="products.last_page > 1" class="mt-10 flex justify-center">
          <nav class="flex items-center space-x-1">
            <Link v-for="(link, index) in products.links" :key="index" :href="link.url || '#'" 
                  class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors"
                  :class="{
                    'bg-blue-600 text-white border-blue-600': link.active, 
                    'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800': !link.active && link.url,
                    'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-400 cursor-not-allowed': !link.url
                  }" v-html="link.label">
            </Link>
          </nav>
        </div>

      </div>
    </div>
  </MainLayout>
</template>

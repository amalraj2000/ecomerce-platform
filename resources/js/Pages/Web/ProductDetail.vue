<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  product: Object,
  relatedProducts: Array
});

// Image Gallery
const activeImage = ref(props.product.images?.length > 0 ? props.product.images.find(i => i.is_primary)?.image_url || props.product.images[0].image_url : null);
const isZoomed = ref(false);
const zoomStyle = ref({});

const handleZoom = (e) => {
  const { left, top, width, height } = e.target.getBoundingClientRect();
  const x = ((e.clientX - left) / width) * 100;
  const y = ((e.clientY - top) / height) * 100;
  zoomStyle.value = { transformOrigin: `${x}% ${y}%`, transform: 'scale(2)' };
};

// Variations
const colors = computed(() => props.product.colors || []);
const sizes = computed(() => props.product.sizes || []);
const selectedColor = ref(colors.value.length > 0 ? colors.value[0] : null);
const selectedSize = ref(sizes.value.length > 0 ? sizes.value[0] : null);

// Pincode Checker
const pincode = ref('');
const deliveryMessage = ref('');
const checkDelivery = () => {
  if (pincode.value.length >= 5) {
    const date = new Date();
    date.setDate(date.getDate() + 3);
    deliveryMessage.value = `Delivery by ${date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })}`;
  } else {
    deliveryMessage.value = 'Please enter a valid PIN code.';
  }
};

const discountedPrice = computed(() => {
  return (props.product.price * (1 - props.product.discount_percentage / 100)).toFixed(2);
});

import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const cartForm = useForm({
    product_id: props.product.id,
    quantity: 1,
    color: selectedColor,
    size: selectedSize,
});

const addToCart = () => {
    if (!page.props.auth.user) {
        window.location.href = route('login');
        return;
    }
    cartForm.post(route('cart.store'), {
        preserveScroll: true
      });
};

const buyNow = () => {
    if (!page.props.auth.user) {
        window.location.href = route('login');
        return;
    }
    cartForm.post(route('cart.store'), {
        onSuccess: () => window.location.href = route('checkout.index'),
    });
};
</script>

<template>
  <Head :title="`${product.title} - KartFlip`" />

  <MainLayout>
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
      <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
          <Link href="/" class="hover:text-blue-600 transition-colors">Home</Link>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="w-3 h-3 mx-1 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <Link :href="'/catalog?category=' + product.category?.slug" class="ml-1 hover:text-blue-600 transition-colors">{{ product.category?.name || 'Category' }}</Link>
          </div>
        </li>
        <li aria-current="page">
          <div class="flex items-center">
            <svg class="w-3 h-3 mx-1 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <span class="ml-1 text-slate-900 dark:text-slate-200 font-medium truncate max-w-xs">{{ product.title }}</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-10">
      <div class="flex flex-col lg:flex-row gap-12">
        
        <!-- Left: Image Gallery -->
        <div class="w-full lg:w-5/12 flex flex-col-reverse md:flex-row gap-4">
          <!-- Thumbnails -->
          <div class="flex md:flex-col gap-3 overflow-x-auto md:overflow-y-auto max-h-[500px] no-scrollbar">
             <!-- Placeholder Thumbnails if no images -->
            <button v-if="!product.images || product.images.length === 0" class="w-16 h-16 md:w-20 md:h-20 rounded-lg border-2 border-blue-600 bg-slate-100 dark:bg-slate-800 flex-shrink-0 relative overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center text-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
            </button>
            
            <button v-for="image in product.images" :key="image.id" @click="activeImage = image.image_url" 
              class="w-16 h-16 md:w-20 md:h-20 rounded-lg border-2 flex-shrink-0 overflow-hidden bg-slate-100 transition-all"
              :class="activeImage === image.image_url ? 'border-blue-600' : 'border-transparent hover:border-blue-300'">
              <img :src="image.image_url" class="w-full h-full object-cover">
            </button>
          </div>
          
          <!-- Main Image with Zoom -->
          <div class="flex-1 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden relative cursor-crosshair group aspect-square md:aspect-auto"
               @mousemove="isZoomed = true; handleZoom($event)" @mouseleave="isZoomed = false; zoomStyle = {}">
            <div class="absolute inset-0 flex items-center justify-center text-slate-400" v-if="!activeImage">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <img v-if="activeImage" :src="activeImage" class="w-full h-full object-cover transition-transform duration-200" :style="isZoomed ? zoomStyle : {}" />
          </div>
        </div>

        <!-- Right: Product Details -->
        <div class="w-full lg:w-7/12 flex flex-col">
          <div class="mb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ product.title }}</h1>
            <div class="flex items-center space-x-4">
              <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ product.brand || 'No Brand' }}</span>
              <div class="flex items-center text-sm text-slate-600 dark:text-slate-400">
                <span class="flex items-center text-yellow-500 mr-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  4.8
                </span>
                (124 reviews)
              </div>
            </div>
          </div>

          <div class="mb-6 border-b border-slate-200 dark:border-slate-800 pb-6">
            <div class="flex items-end space-x-3 mb-2">
              <span class="text-4xl font-black text-slate-900 dark:text-white">${{ discountedPrice }}</span>
              <span v-if="product.discount_percentage > 0" class="text-lg text-slate-500 line-through mb-1">${{ product.price }}</span>
              <span v-if="product.discount_percentage > 0" class="text-lg font-bold text-green-600 dark:text-green-400 mb-1">{{ product.discount_percentage }}% off</span>
            </div>
            <p class="text-sm text-green-600 dark:text-green-500 font-medium">Inclusive of all taxes</p>
          </div>

          <!-- Variations: Color -->
          <div v-if="colors.length > 0" class="mb-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-3">Color: <span class="font-normal text-slate-600 dark:text-slate-400">{{ selectedColor }}</span></h3>
            <div class="flex space-x-3">
              <button v-for="color in colors" :key="color" @click="selectedColor = color"
                class="px-4 py-1.5 rounded-full border-2 focus:outline-none transition-all font-medium text-sm shadow-sm"
                :class="selectedColor === color ? 'border-blue-600 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20'">
                {{ color }}
              </button>
            </div>
          </div>

          <!-- Variations: Size -->
          <div v-if="sizes.length > 0" class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-8">
            <div class="flex justify-between items-center mb-3">
              <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Size: <span class="font-normal text-slate-600 dark:text-slate-400">{{ selectedSize }}</span></h3>
            </div>
            <div class="flex flex-wrap gap-3">
              <button v-for="size in sizes" :key="size" @click="selectedSize = size"
                class="px-5 py-2 rounded-lg border font-medium transition-all shadow-sm"
                :class="selectedSize === size ? 'border-blue-600 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20'">
                {{ size }}
              </button>
            </div>
          </div>

          <!-- Seller Info & Delivery -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Delivery -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
              <div class="flex items-center space-x-2 text-slate-900 dark:text-white font-semibold mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Delivery Options</span>
              </div>
              <div class="flex space-x-2">
                <input v-model="pincode" type="text" placeholder="Enter PIN code" class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button @click="checkDelivery" class="bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">Check</button>
              </div>
              <p v-if="deliveryMessage" class="mt-2 text-sm font-medium" :class="pincode.length >= 5 ? 'text-green-600' : 'text-red-500'">{{ deliveryMessage }}</p>
            </div>
            
            <!-- Seller Box -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700 flex flex-col justify-center">
              <div class="text-sm text-slate-500 dark:text-slate-400 mb-1">Sold by</div>
              <div class="flex items-center space-x-2">
                <span class="font-bold text-slate-900 dark:text-white text-lg">{{ product.vendor?.store_name || 'KartFlip Retail' }}</span>
                <span v-if="product.vendor?.is_verified !== false" class="bg-blue-600 text-white rounded-full p-0.5" title="Verified Seller">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </span>
              </div>
              <div class="flex items-center mt-1">
                <span class="bg-green-600 text-white text-xs font-bold px-1.5 py-0.5 rounded flex items-center">
                  {{ product.vendor?.rating || '4.5' }} <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-0.5" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                </span>
              </div>
            </div>
          </div>

          <!-- Add to Cart / Buy Now -->
          <div v-if="product.stock <= 0" class="mt-auto">
             <div class="w-full bg-red-50 text-red-700 font-bold py-4 px-8 rounded-xl text-center text-lg border border-red-200 shadow-sm uppercase tracking-wider">
               Out of Stock
             </div>
          </div>
          <div v-else class="flex flex-col sm:flex-row gap-4 mt-auto">
            <button @click="addToCart" :disabled="cartForm.processing" class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-4 px-8 rounded-xl shadow-sm transition-all transform hover:-translate-y-1 flex items-center justify-center space-x-2 disabled:opacity-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span>Add to Cart</span>
            </button>
            <button @click="buyNow" :disabled="cartForm.processing" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-xl shadow-sm transition-all transform hover:-translate-y-1 flex items-center justify-center space-x-2 disabled:opacity-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              <span>Buy Now</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Description & Reviews Section -->
    <div class="mt-12 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-10">
      <div class="border-b border-slate-200 dark:border-slate-800 mb-6 flex space-x-8">
        <button class="border-b-2 border-blue-600 text-blue-600 font-bold py-3 px-2">Description</button>
        <button class="text-slate-500 font-medium py-3 px-2 hover:text-slate-900 dark:hover:text-white transition-colors">Reviews</button>
      </div>
      
      <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300">
        <p>{{ product.description }}</p>
        <p v-if="!product.description">No description available for this product.</p>
      </div>
    </div>

  </MainLayout>
</template>

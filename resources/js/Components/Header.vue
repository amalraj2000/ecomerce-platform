<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const searchQuery = ref('');
const isMegaMenuOpen = ref(false);

const categories = computed(() => page.props.globalCategories || []);
</script>

<template>
  <header class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <Link href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent hover:scale-105 transition-transform">
            KartFlip
          </Link>
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-2xl mx-8 relative group">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search for products, brands and more..." 
            class="w-full bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-full py-2.5 pl-5 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 border-none transition-all group-hover:bg-slate-200 dark:group-hover:bg-slate-800"
          >
          <button class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>

        <!-- Navigation & Actions -->
        <div class="flex items-center space-x-6">
          <template v-if="!user">
            <Link :href="route('login')" class="text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
              Login
            </Link>
          </template>
          
          <template v-else>
            <div class="relative">
              <Dropdown align="right" width="48">
                  <template #trigger>
                      <button class="flex items-center text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 focus:outline-none transition ease-in-out duration-150">
                          <div>Hi, {{ user.name }}</div>
                          <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                          </svg>
                      </button>
                  </template>

                  <template #content>
                      <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                      <DropdownLink v-if="user.role === 'admin'" :href="route('admin.dashboard')">Admin Panel</DropdownLink>
                      <DropdownLink v-if="user.role === 'vendor'" :href="route('vendor.dashboard')">Vendor Dashboard</DropdownLink>
                      <DropdownLink v-if="user.role === 'user'" :href="route('user.orders')">My Orders</DropdownLink>
                      <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                  </template>
              </Dropdown>
            </div>
          </template>
          
          <Link :href="route('cart.index')" class="relative p-2 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span v-if="user" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
              <!-- Cart count could be passed via HandleInertiaRequests -->
              *
            </span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Categories Nav -->
    <div class="border-t border-slate-100 dark:border-slate-800 hidden md:block bg-white/50 dark:bg-slate-900/50 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ul class="flex space-x-8 py-3 overflow-x-auto no-scrollbar">
          <li>
            <Link :href="route('home')" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors whitespace-nowrap py-2 flex items-center">
              Home
            </Link>
          </li>
          <li v-for="category in categories" :key="category.slug" @mouseenter="isMegaMenuOpen = true" @mouseleave="isMegaMenuOpen = false">
            <Link :href="route('catalog.index', { category: category.slug })" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors whitespace-nowrap py-2 flex items-center">
              {{ category.name }}
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </Link>
          </li>
        </ul>
      </div>
    </div>
  </header>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
